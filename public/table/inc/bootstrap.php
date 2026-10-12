<?php
declare(strict_types=1);

define('APP_ROOT', dirname(__DIR__));

if (!is_file(APP_ROOT . '/config.php')) {
    http_response_code(500);
    exit('config.php is missing. Copy config.sample.php to config.php and fill it in.');
}

$GLOBALS['CFG'] = require APP_ROOT . '/config.php';

function cfg(string $key, $default = null)
{
    return $GLOBALS['CFG'][$key] ?? $default;
}

function db(): PDO
{
    static $pdo = null;
    if ($pdo === null) {
        $dsn = sprintf('mysql:host=%s;dbname=%s;charset=utf8mb4', cfg('db_host'), cfg('db_name'));
        $pdo = new PDO($dsn, cfg('db_user'), cfg('db_pass'), [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]);
    }
    return $pdo;
}

function q(string $sql, array $params = []): PDOStatement
{
    $st = db()->prepare($sql);
    $st->execute($params);
    return $st;
}

function start_session(): void
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_name('kukuku_admin');
        session_set_cookie_params([
            'lifetime' => 0,
            'path'     => '/',
            'httponly' => true,
            'samesite' => 'Lax',
            'secure'   => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
        ]);
        session_start();
    }
}

function is_admin(): bool
{
    start_session();
    return !empty($_SESSION['is_admin']);
}

function admin_login(string $password): bool
{
    start_session();
    $ok = $password !== '' && hash_equals((string) cfg('admin_password'), $password);
    if ($ok) {
        session_regenerate_id(true);
        $_SESSION['is_admin'] = true;
        $_SESSION['csrf'] = bin2hex(random_bytes(16));
    }
    return $ok;
}

function csrf_token(): string
{
    start_session();
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(16));
    }
    return $_SESSION['csrf'];
}

function check_csrf(?string $token): bool
{
    start_session();
    return !empty($_SESSION['csrf']) && is_string($token) && hash_equals($_SESSION['csrf'], $token);
}

function base_url(): string
{
    $b = (string) cfg('base_url', '');
    if ($b !== '') {
        return rtrim($b, '/');
    }
    $https = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';
    $host  = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $dir   = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '/')), '/');
    return ($https ? 'https' : 'http') . '://' . $host . $dir;
}

function h(?string $s): string
{
    return htmlspecialchars((string) $s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function json_out(array $data, int $status = 200): void
{
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    header('Cache-Control: no-store');
    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

function fail(string $code, int $status = 400): void
{
    json_out(['ok' => false, 'error' => $code], $status);
}

function client_ip(): string
{
    return substr((string) ($_SERVER['REMOTE_ADDR'] ?? '0.0.0.0'), 0, 45);
}

function asset_v(string $path): string
{
    $f = APP_ROOT . '/' . $path;
    return $path . '?v=' . (is_file($f) ? filemtime($f) : '1');
}

function media_url(?string $file): ?string
{
    return $file ? 'uploads/' . $file : null;
}

function valid_lang(?string $l): string
{
    return in_array($l, ['en', 'ru', 'el'], true) ? $l : 'en';
}
