<?php
declare(strict_types=1);

require_once __DIR__ . '/ai.php';

const ACTIVE_WINDOW = 25; // seconds since last poll to count a player as "at the table"

function game_get(int $id): ?array
{
    $g = q('SELECT * FROM games WHERE id = ?', [$id])->fetch();
    return $g ?: null;
}

function game_bump(int $id): void
{
    q('UPDATE games SET version = version + 1 WHERE id = ?', [$id]);
}

function new_code(): string
{
    $ttl = time() - (int) cfg('code_ttl_hours', 6) * 3600;
    for ($i = 0; $i < 50; $i++) {
        $code = str_pad((string) random_int(0, 9999), 4, '0', STR_PAD_LEFT);
        $busy = q("SELECT 1 FROM games WHERE code = ? AND phase <> 'finished' AND created_at > ?", [$code, $ttl])->fetch();
        if (!$busy) {
            return $code;
        }
    }
    throw new RuntimeException('No free code');
}

function game_create(string $hostLang = 'en'): int
{
    $now = time();
    q('INSERT INTO games (code, phase, position, host_lang, created_at, phase_started) VALUES (?, ?, -1, ?, ?, ?)',
        [new_code(), 'lobby', valid_lang($hostLang), $now, $now]);
    $gid = (int) db()->lastInsertId();

    $tracks = q('SELECT id FROM tracks WHERE active = 1 ORDER BY sort_order, id LIMIT ' . (int) cfg('max_tracks', 10))->fetchAll();
    foreach ($tracks as $i => $t) {
        q('INSERT INTO game_tracks (game_id, position, track_id) VALUES (?, ?, ?)', [$gid, $i, $t['id']]);
    }
    return $gid;
}

function game_find_by_code(string $code): ?array
{
    $ttl = time() - (int) cfg('code_ttl_hours', 6) * 3600;
    $g = q("SELECT * FROM games WHERE code = ? AND phase <> 'finished' AND created_at > ? ORDER BY id DESC LIMIT 1",
        [$code, $ttl])->fetch();
    return $g ?: null;
}

/** Tracks of the game in play order, with card owners. */
function game_cards(int $gid): array
{
    return q('SELECT gt.position, t.id AS track_id, t.title, t.cover_file, t.audio_file, t.start_sec, t.apple_url,
                     p.id AS owner_id
              FROM game_tracks gt
              JOIN tracks t ON t.id = gt.track_id
              LEFT JOIN players p ON p.game_id = gt.game_id AND p.track_id = t.id
              WHERE gt.game_id = ?
              ORDER BY gt.position', [$gid])->fetchAll();
}

function players_count(int $gid): array
{
    $since = time() - ACTIVE_WINDOW;
    $r = q('SELECT COUNT(*) AS total, SUM(last_seen >= ?) AS active FROM players WHERE game_id = ?', [$since, $gid])->fetch();
    return ['total' => (int) $r['total'], 'active' => (int) $r['active']];
}

function answered_count(int $gid, int $pos): int
{
    return (int) q('SELECT COUNT(*) FROM answers WHERE game_id = ? AND position = ?', [$gid, $pos])->fetchColumn();
}

function summary_get(int $gid, int $pos): ?array
{
    $r = q('SELECT source, body_json FROM summaries WHERE game_id = ? AND position = ?', [$gid, $pos])->fetch();
    if (!$r) {
        return null;
    }
    return ['source' => $r['source'], 'data' => json_decode($r['body_json'], true)];
}

// ---------------------------------------------------------------- host actions

function host_start(array $g, bool $onlyClaimed): void
{
    if ($g['phase'] !== 'lobby') {
        return;
    }
    $gid = (int) $g['id'];
    if ($onlyClaimed) {
        $claimed = q('SELECT gt.track_id FROM game_tracks gt
                      JOIN players p ON p.game_id = gt.game_id AND p.track_id = gt.track_id
                      WHERE gt.game_id = ? ORDER BY gt.position', [$gid])->fetchAll(PDO::FETCH_COLUMN);
        if ($claimed) {
            q('DELETE FROM game_tracks WHERE game_id = ?', [$gid]);
            foreach ($claimed as $i => $tid) {
                q('INSERT INTO game_tracks (game_id, position, track_id) VALUES (?, ?, ?)', [$gid, $i, $tid]);
            }
        }
    }
    q("UPDATE games SET phase = 'listen', position = 0, phase_started = ?, version = version + 1
       WHERE id = ? AND phase = 'lobby'", [time(), $gid]);
}

function host_to_answer(array $g, int $pos): void
{
    q("UPDATE games SET phase = 'answer', phase_started = ?, answer_deadline = ?, version = version + 1
       WHERE id = ? AND phase = 'listen' AND position = ?",
        [time(), time() + (int) cfg('answer_seconds', 60), $g['id'], $pos]);
}

function host_summarize(array $g, int $pos): void
{
    $gid = (int) $g['id'];
    $now = time();
    // Lock: only one request does the AI call. A stuck "summarizing" can be retried after 40 s.
    $locked = q("UPDATE games SET phase = 'summarizing', phase_started = ?, version = version + 1
                 WHERE id = ? AND position = ? AND (phase = 'answer' OR (phase = 'summarizing' AND phase_started < ?))",
        [$now, $gid, $pos, $now - 40])->rowCount();
    if ($locked !== 1) {
        return;
    }

    $card = q('SELECT t.title FROM game_tracks gt JOIN tracks t ON t.id = gt.track_id WHERE gt.game_id = ? AND gt.position = ?',
        [$gid, $pos])->fetch();
    $comments = q("SELECT body FROM answers WHERE game_id = ? AND position = ? AND body <> ''", [$gid, $pos])
        ->fetchAll(PDO::FETCH_COLUMN);

    $ai = ai_summary((string) ($card['title'] ?? ''), $comments);
    if ($ai) {
        $source = 'ai';
        $body = $ai;
    } else {
        shuffle($comments);
        $quotes = array_map(fn($c) => mb_substr(trim((string) $c), 0, 120), array_slice($comments, 0, 3));
        $source = 'fallback';
        $body = ['quotes' => $quotes];
    }

    q('REPLACE INTO summaries (game_id, position, source, body_json, created_at) VALUES (?, ?, ?, ?, ?)',
        [$gid, $pos, $source, json_encode($body, JSON_UNESCAPED_UNICODE), time()]);
    q("UPDATE games SET phase = 'summary', phase_started = ?, version = version + 1 WHERE id = ? AND position = ?",
        [time(), $gid, $pos]);
}

function host_next(array $g, int $pos): void
{
    $gid = (int) $g['id'];
    $total = (int) q('SELECT COUNT(*) FROM game_tracks WHERE game_id = ?', [$gid])->fetchColumn();
    if ($pos + 1 >= $total) {
        host_finish($g);
        return;
    }
    q("UPDATE games SET phase = 'listen', position = ?, phase_started = ?, answer_deadline = 0, version = version + 1
       WHERE id = ? AND phase = 'summary' AND position = ?", [$pos + 1, time(), $gid, $pos]);
}

function host_finish(array $g): void
{
    $gid = (int) $g['id'];
    q("UPDATE games SET phase = 'finished', finished_at = ?, phase_started = ?, version = version + 1 WHERE id = ?",
        [time(), time(), $gid]);
    if (cfg('delete_answers_after_game', true)) {
        q('DELETE FROM answers WHERE game_id = ?', [$gid]);
    }
}

// ---------------------------------------------------------------- state builders

function card_public(array $c): array
{
    return [
        'track_id' => (int) $c['track_id'],
        'title'    => $c['title'],
        'cover'    => media_url($c['cover_file']),
        'taken'    => $c['owner_id'] !== null,
    ];
}

function state_common(array $g): array
{
    $gid   = (int) $g['id'];
    $cards = game_cards($gid);
    $pos   = (int) $g['position'];
    $cur   = null;
    foreach ($cards as $c) {
        if ((int) $c['position'] === $pos) {
            $cur = $c;
        }
    }
    $state = [
        'ok'   => true,
        'now'  => time(),
        'game' => [
            'id'              => $gid,
            'phase'           => $g['phase'],
            'position'        => $pos,
            'total'           => count($cards),
            'phase_started'   => (int) $g['phase_started'],
            'answer_deadline' => (int) $g['answer_deadline'],
            'listen_seconds'  => (int) cfg('listen_seconds', 90),
            'host_lang'       => $g['host_lang'],
            'version'         => (int) $g['version'],
        ],
        'cards'   => array_map('card_public', $cards),
        'players' => players_count($gid),
        'links'   => ['music' => cfg('music_url'), 'site' => cfg('site_url')],
        'current' => null,
        'summary' => null,
    ];
    if ($cur) {
        $state['current'] = [
            'track_id'  => (int) $cur['track_id'],
            'title'     => $cur['title'],
            'cover'     => media_url($cur['cover_file']),
            'has_owner' => $cur['owner_id'] !== null,
            'owner_id'  => $cur['owner_id'] !== null ? (int) $cur['owner_id'] : null,
        ];
        $state['answered'] = answered_count($gid, $pos);
        if ($g['phase'] === 'summary') {
            $state['summary'] = summary_get($gid, $pos);
        }
    }
    return $state;
}

function state_host(array $g): array
{
    $s = state_common($g);
    $s['game']['code'] = $g['code'];
    $s['join_url'] = base_url() . '/?code=' . $g['code'];
    $s['min_players'] = (int) cfg('min_players', 2);
    if ($s['current']) {
        $cards = game_cards((int) $g['id']);
        foreach ($cards as $c) {
            if ((int) $c['position'] === (int) $g['position']) {
                $s['current']['audio'] = media_url($c['audio_file']);
                $s['current']['start_sec'] = (int) $c['start_sec'];
            }
        }
        unset($s['current']['owner_id']);
    }
    return $s;
}

function state_player(array $g, array $p): array
{
    $s = state_common($g);
    $pid = (int) $p['id'];
    $myCard = null;
    foreach ($s['cards'] as $c) {
        if ($p['track_id'] !== null && $c['track_id'] === (int) $p['track_id']) {
            $myCard = $c;
        }
    }
    $answered = false;
    if ($s['current']) {
        $answered = (bool) q('SELECT 1 FROM answers WHERE game_id = ? AND position = ? AND player_id = ?',
            [$g['id'], $g['position'], $pid])->fetch();
        $s['current']['is_mine'] = $s['current']['owner_id'] === $pid;
        unset($s['current']['owner_id']);
    }
    $lead = (bool) q('SELECT 1 FROM leads WHERE player_id = ?', [$pid])->fetch();
    $s['me'] = [
        'lang'      => $p['lang'],
        'card'      => $myCard,
        'answered'  => $answered,
        'lead_done' => $lead,
    ];
    return $s;
}
