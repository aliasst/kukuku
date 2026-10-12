<?php
// Copy this file to config.php and fill in your values.

return [
    // --- Database (MySQL / MariaDB) ---
    'db_host' => 'localhost',
    'db_name' => 'kukuku_game',
    'db_user' => 'kukuku',
    'db_pass' => 'change-me',

    // --- Password for the host tablet and the admin panel ---
    // Only someone with this password can open a table and start a game.
    'admin_password' => 'change-me-too',

    // --- AI summary (Anthropic Claude API) ---
    // Get a key at https://console.anthropic.com  — leave empty to use the no-AI fallback.
    'ai_api_key' => '',
    'ai_model'   => 'claude-haiku-4-5-20251001',
    'ai_timeout' => 15,            // seconds; after that the fallback is shown

    // --- Game settings ---
    'max_tracks'      => 10,       // tracks per game
    'max_players'     => 10,
    'min_players'     => 2,        // the Start button unlocks from this number
    'listen_seconds'  => 90,       // length of each fragment
    'answer_seconds'  => 60,       // time to write a comment
    'delete_answers_after_game' => true,   // guests' comments are wiped when the game ends
    'code_ttl_hours'  => 6,        // a table code works this long

    // --- Links shown as QR codes at the end ---
    'music_url' => 'https://music.apple.com/us/artist/funkafunka/6791572586',
    'site_url'  => 'https://kukuku.club/',

    // Public base URL of this app, used for the join QR (no trailing slash).
    // Leave empty to detect automatically.
    'base_url'  => '',
];
