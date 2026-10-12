<?php
declare(strict_types=1);

require __DIR__ . '/inc/bootstrap.php';
require __DIR__ . '/inc/game.php';

const PLAYER_COOKIE = 'kk_player';

$action = (string) ($_GET['a'] ?? '');
$in = json_decode((string) file_get_contents('php://input'), true);
if (!is_array($in)) {
    $in = $_POST;
}

try {
    if (str_starts_with($action, 'host_')) {
        handle_host($action, $in);
    } else {
        handle_player($action, $in);
    }
} catch (Throwable $e) {
    error_log('[kukuku] ' . $e->getMessage() . ' @ ' . $e->getFile() . ':' . $e->getLine());
    fail('server_error', 500);
}

// ======================================================================== player

function current_player(): ?array
{
    $token = (string) ($_COOKIE[PLAYER_COOKIE] ?? '');
    if (!preg_match('/^[a-f0-9]{32}$/', $token)) {
        return null;
    }
    $p = q('SELECT * FROM players WHERE token = ?', [$token])->fetch();
    return $p ?: null;
}

function set_player_cookie(string $token): void
{
    setcookie(PLAYER_COOKIE, $token, [
        'expires'  => time() + 12 * 3600,
        'path'     => '/',
        'httponly' => true,
        'samesite' => 'Lax',
        'secure'   => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
    ]);
}

function handle_player(string $action, array $in): void
{
    if ($action === 'join') {
        $ip = client_ip();
        q('DELETE FROM join_attempts WHERE created_at < ?', [time() - 600]);
        $tries = (int) q('SELECT COUNT(*) FROM join_attempts WHERE ip = ? AND created_at > ?', [$ip, time() - 600])->fetchColumn();
        if ($tries >= 15) {
            fail('too_many', 429);
        }
        $code = preg_replace('/\D/', '', (string) ($in['code'] ?? ''));
        $g = strlen($code) === 4 ? game_find_by_code($code) : null;
        if (!$g) {
            q('INSERT INTO join_attempts (ip, created_at) VALUES (?, ?)', [$ip, time()]);
            fail('bad_code');
        }

        // Re-joining the same table from the same phone keeps the same card.
        $existing = current_player();
        if ($existing && (int) $existing['game_id'] === (int) $g['id']) {
            q('UPDATE players SET lang = ?, last_seen = ? WHERE id = ?', [valid_lang($in['lang'] ?? 'en'), time(), $existing['id']]);
            json_out(['ok' => true]);
        }

        $count = (int) q('SELECT COUNT(*) FROM players WHERE game_id = ?', [$g['id']])->fetchColumn();
        if ($count >= (int) cfg('max_players', 10)) {
            fail('table_full');
        }
        $token = bin2hex(random_bytes(16));
        q('INSERT INTO players (game_id, token, lang, last_seen, created_at) VALUES (?, ?, ?, ?, ?)',
            [$g['id'], $token, valid_lang($in['lang'] ?? 'en'), time(), time()]);
        set_player_cookie($token);
        game_bump((int) $g['id']);
        json_out(['ok' => true]);
    }

    $p = current_player();
    if (!$p) {
        fail('no_player', 401);
    }
    $g = game_get((int) $p['game_id']);
    if (!$g) {
        fail('no_game', 404);
    }
    $pid = (int) $p['id'];
    $gid = (int) $g['id'];

    switch ($action) {
        case 'state':
            q('UPDATE players SET last_seen = ? WHERE id = ?', [time(), $pid]);
            json_out(state_player($g, $p));

        case 'lang':
            q('UPDATE players SET lang = ? WHERE id = ?', [valid_lang($in['lang'] ?? 'en'), $pid]);
            json_out(['ok' => true]);

        case 'claim':
            if ($g['phase'] === 'finished') {
                fail('finished');
            }
            $tid = (int) ($in['track_id'] ?? 0);
            $inGame = q('SELECT 1 FROM game_tracks WHERE game_id = ? AND track_id = ?', [$gid, $tid])->fetch();
            if (!$inGame) {
                fail('bad_card');
            }
            if ($g['phase'] !== 'lobby' && $p['track_id'] !== null) {
                fail('locked'); // the card can be changed only before the start
            }
            try {
                q('UPDATE players SET track_id = ? WHERE id = ?', [$tid, $pid]);
            } catch (PDOException $e) {
                fail('card_taken'); // unique (game_id, track_id)
            }
            game_bump($gid);
            json_out(['ok' => true]);

        case 'answer':
            if ($g['phase'] !== 'answer') {
                fail('not_now');
            }
            $body = !empty($in['skip']) ? '' : trim((string) ($in['text'] ?? ''));
            $body = mb_substr(preg_replace('/\s+/u', ' ', $body), 0, 200);
            try {
                q('INSERT INTO answers (game_id, position, player_id, body, created_at) VALUES (?, ?, ?, ?, ?)',
                    [$gid, $g['position'], $pid, $body, time()]);
            } catch (PDOException $e) {
                fail('already');
            }
            game_bump($gid);
            json_out(['ok' => true]);

        case 'lead':
            if ($g['phase'] !== 'finished') {
                fail('not_now');
            }
            $name  = trim(mb_substr((string) ($in['name'] ?? ''), 0, 120));
            $phone = trim(mb_substr((string) ($in['phone'] ?? ''), 0, 40));
            $email = trim(mb_substr((string) ($in['email'] ?? ''), 0, 160));
            $insta = ltrim(trim(mb_substr((string) ($in['instagram'] ?? ''), 0, 80)), '@');
            $consent = !empty($in['consent']) ? 1 : 0;
            if ($name === '') {
                fail('need_name');
            }
            if ($phone === '' && $email === '' && $insta === '') {
                fail('need_contact');
            }
            if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                fail('bad_email');
            }
            if (!$consent) {
                fail('need_consent');
            }
            $card = $p['track_id'] ? q('SELECT title FROM tracks WHERE id = ?', [$p['track_id']])->fetchColumn() : null;
            q('INSERT INTO leads (game_id, player_id, card_title, name, phone, email, instagram, lang, consent, created_at)
               VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
               ON DUPLICATE KEY UPDATE name = VALUES(name), phone = VALUES(phone), email = VALUES(email),
                                       instagram = VALUES(instagram), consent = VALUES(consent)',
                [$gid, $pid, $card ?: null, $name, $phone ?: null, $email ?: null, $insta ?: null, $p['lang'], $consent, time()]);
            json_out(['ok' => true]);

        case 'leave':
            setcookie(PLAYER_COOKIE, '', ['expires' => time() - 3600, 'path' => '/']);
            json_out(['ok' => true]);
    }
    fail('unknown_action', 404);
}

// ======================================================================== host

function handle_host(string $action, array $in): void
{
    if (!is_admin()) {
        fail('auth', 401);
    }
    if (!check_csrf($_SERVER['HTTP_X_CSRF'] ?? null)) {
        fail('csrf', 403);
    }

    if ($action === 'host_create') {
        $gid = game_create(valid_lang($in['lang'] ?? 'en'));
        $_SESSION['host_game'] = $gid;
        json_out(['ok' => true, 'game_id' => $gid]);
    }

    $gid = (int) ($in['game_id'] ?? 0);
    $g = game_get($gid);
    if (!$g) {
        fail('no_game', 404);
    }
    $pos = (int) ($in['position'] ?? -1);

    switch ($action) {
        case 'host_state':
            json_out(state_host($g));
        case 'host_lang':
            q('UPDATE games SET host_lang = ?, version = version + 1 WHERE id = ?', [valid_lang($in['lang'] ?? 'en'), $gid]);
            break;
        case 'host_start':
            host_start($g, !empty($in['only_claimed']));
            break;
        case 'host_to_answer':
            host_to_answer($g, $pos);
            break;
        case 'host_summarize':
            host_summarize($g, $pos);
            break;
        case 'host_next':
            host_next($g, $pos);
            break;
        case 'host_finish':
            host_finish($g);
            break;
        case 'host_kick':
            q('DELETE FROM players WHERE id = ? AND game_id = ?', [(int) ($in['player_id'] ?? 0), $gid]);
            game_bump($gid);
            break;
        default:
            fail('unknown_action', 404);
    }
    json_out(state_host(game_get($gid)));
}
