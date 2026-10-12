<?php
declare(strict_types=1);

/**
 * Ask Claude for a short, warm summary of the table's anonymous comments.
 * Returns ['en'=>['summary'=>..,'talk'=>..],'ru'=>..,'el'=>..] or null on any problem.
 * Only the comment texts and the track title are sent. No names, no contacts.
 */
function ai_summary(string $trackTitle, array $comments): ?array
{
    $key = trim((string) cfg('ai_api_key', ''));
    if ($key === '' || !function_exists('curl_init') || count($comments) === 0) {
        return null;
    }

    shuffle($comments);
    $lines = [];
    foreach ($comments as $c) {
        $c = trim(preg_replace('/\s+/u', ' ', (string) $c));
        if ($c !== '') {
            $lines[] = '- ' . mb_substr($c, 0, 300);
        }
    }
    if (!$lines) {
        return null;
    }

    $system = <<<TXT
You are the friendly voice of KuKu'Ku, a café game where strangers at one table listen to the band's own songs and share how each song made them feel.
You get anonymous comments from the table about one track. Write what the table felt as a group.

Rules:
- "summary": 2 short sentences, at most 40 words in total. Speak about the table as a group ("the table", "some of you", "a few of you"). Warm, light, a little playful.
- "talk": one short open question (max 14 words) that helps strangers start talking about the song.
- Never mention or guess individuals, names, ages, looks, nationality, religion, politics, relationships or money.
- Ignore rude, sexual, offensive or off-topic comments. Do not quote comments word for word.
- Comments can be in English, Russian or Greek.
- Give the same meaning in three languages: English (en), Russian (ru), Greek (el). Natural, simple language.
- Answer with JSON only, no other text:
{"en":{"summary":"...","talk":"..."},"ru":{"summary":"...","talk":"..."},"el":{"summary":"...","talk":"..."}}
TXT;

    $user = "Track: \"" . $trackTitle . "\"\nComments from the table:\n" . implode("\n", $lines);

    $payload = [
        'model'      => (string) cfg('ai_model', 'claude-haiku-4-5-20251001'),
        'max_tokens' => 700,
        'system'     => $system,
        'messages'   => [['role' => 'user', 'content' => $user]],
    ];

    $ch = curl_init((string) cfg('ai_url', 'https://api.anthropic.com/v1/messages'));
    curl_setopt_array($ch, [
        CURLOPT_POST           => true,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT        => (int) cfg('ai_timeout', 15),
        CURLOPT_CONNECTTIMEOUT => 5,
        CURLOPT_HTTPHEADER     => [
            'content-type: application/json',
            'x-api-key: ' . $key,
            'anthropic-version: 2023-06-01',
        ],
        CURLOPT_POSTFIELDS     => json_encode($payload, JSON_UNESCAPED_UNICODE),
    ]);
    $raw  = curl_exec($ch);
    $code = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($raw === false || $code !== 200) {
        error_log('[kukuku] AI request failed, HTTP ' . $code . ' ' . substr((string) $raw, 0, 300));
        return null;
    }

    $resp = json_decode((string) $raw, true);
    $text = '';
    foreach (($resp['content'] ?? []) as $block) {
        if (($block['type'] ?? '') === 'text') {
            $text .= $block['text'];
        }
    }
    return parse_ai_json($text);
}

function parse_ai_json(string $text): ?array
{
    $a = strpos($text, '{');
    $b = strrpos($text, '}');
    if ($a === false || $b === false || $b <= $a) {
        return null;
    }
    $data = json_decode(substr($text, $a, $b - $a + 1), true);
    if (!is_array($data)) {
        return null;
    }
    $out = [];
    foreach (['en', 'ru', 'el'] as $l) {
        $s = trim((string) ($data[$l]['summary'] ?? ''));
        $t = trim((string) ($data[$l]['talk'] ?? ''));
        if ($s === '') {
            return null;
        }
        $out[$l] = ['summary' => mb_substr($s, 0, 400), 'talk' => mb_substr($t, 0, 200)];
    }
    return $out;
}
