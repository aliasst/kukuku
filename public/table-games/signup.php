<?php
// KuKu'Ku Table landing: saves a sign-up to data/signups.csv (the folder is closed to the web).
declare(strict_types=1);
header('Content-Type: application/json; charset=utf-8');
if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') { http_response_code(405); echo '{"ok":false}'; exit; }
$in = json_decode((string) file_get_contents('php://input'), true);
if (!is_array($in)) { http_response_code(400); echo '{"ok":false}'; exit; }
$clean = fn($v, $n) => mb_substr(trim(preg_replace('/[\r\n\t]+/', ' ', (string) $v)), 0, $n);
$name = $clean($in['name'] ?? '', 80);
$contact = $clean($in['contact'] ?? '', 80);
$lang = $clean($in['lang'] ?? '', 10);
$time = implode(' ', array_intersect((array) ($in['time'] ?? []), ['weekday-morning', 'weekday-evening', 'weekend']));
if ($name === '' || mb_strlen($contact) < 5) { http_response_code(422); echo '{"ok":false}'; exit; }
$file = __DIR__ . '/data/signups.csv';
$new = !is_file($file);
$fh = fopen($file, 'a');
if (!$fh) { http_response_code(500); echo '{"ok":false}'; exit; }
flock($fh, LOCK_EX);
if ($new) { fwrite($fh, "\xEF\xBB\xBF"); fputcsv($fh, ['date', 'name', 'contact', 'lang', 'time']); }
// a leading = would run as a formula in Excel
$safe = fn($s) => str_starts_with($s, '=') ? "'" . $s : $s;
fputcsv($fh, [date('Y-m-d H:i'), $safe($name), $safe($contact), $lang, $time]);
flock($fh, LOCK_UN);
fclose($fh);
echo '{"ok":true}';
