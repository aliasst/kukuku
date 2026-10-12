<?php
declare(strict_types=1);
require __DIR__ . '/inc/bootstrap.php';
$code = preg_replace('/\D/', '', (string) ($_GET['code'] ?? ''));
?><!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
<meta name="theme-color" content="#FAEBE1">
<meta name="robots" content="noindex">
<title>KuKu'Ku Table</title>
<link rel="stylesheet" href="<?= h(asset_v('assets/css/fonts.css')) ?>">
<link rel="stylesheet" href="<?= h(asset_v('assets/css/app.css')) ?>">
</head>
<body>
<header class="top">
  <a class="logo" href="./">KuKu'Ku</a>
  <div class="top__right"><div class="langs" id="langs"></div></div>
</header>

<main class="phone" id="app" data-code="<?= h($code) ?>">
  <div class="center muted" style="padding:60px 0"><span class="dots"><i></i><i></i><i></i></span></div>
</main>

<div class="toast" id="toast" role="status" aria-live="polite"></div>

<script src="<?= h(asset_v('assets/js/i18n.js')) ?>"></script>
<script src="<?= h(asset_v('assets/js/common.js')) ?>"></script>
<script src="<?= h(asset_v('assets/js/player.js')) ?>"></script>
</body>
</html>
