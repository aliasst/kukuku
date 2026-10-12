<?php
declare(strict_types=1);
require __DIR__ . '/inc/bootstrap.php';
start_session();

$loginError = '';
if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST' && isset($_POST['password'])) {
    if (admin_login((string) $_POST['password'])) {
        header('Location: host.php');
        exit;
    }
    usleep(700000);
    $loginError = 'Wrong password';
}

$isAdmin = is_admin();
$gid = 0;
if ($isAdmin) {
    if (isset($_GET['game'])) {
        $_SESSION['host_game'] = (int) $_GET['game'];
    }
    $gid = (int) ($_SESSION['host_game'] ?? 0);
    if ($gid && !q('SELECT 1 FROM games WHERE id = ?', [$gid])->fetch()) {
        $gid = 0;
    }
}
?><!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="theme-color" content="#FAEBE1">
<meta name="robots" content="noindex">
<title>KuKu'Ku Table · Host</title>
<link rel="stylesheet" href="<?= h(asset_v('assets/css/fonts.css')) ?>">
<link rel="stylesheet" href="<?= h(asset_v('assets/css/app.css')) ?>">
</head>
<body>
<?php if (!$isAdmin): ?>
<header class="top"><span class="logo">KuKu'Ku</span></header>
<main class="phone">
  <form class="panel stack" method="post" style="margin-top:40px">
    <h1 class="h-m">Host login</h1>
    <p class="muted" style="margin:0">Only the KuKu'Ku team can open a table and start the game.</p>
    <input class="input" type="password" name="password" placeholder="Password" autofocus required>
    <?php if ($loginError): ?><div class="error"><?= h($loginError) ?></div><?php endif; ?>
    <button class="btn btn--primary btn--block" type="submit">Log in</button>
  </form>
</main>
<?php else: ?>
<header class="top">
  <span class="logo">KuKu'Ku</span>
  <div class="top__right">
    <span id="hdr"></span>
    <div class="langs" id="langs"></div>
  </div>
</header>
<main class="host" id="app"
      data-game="<?= (int) $gid ?>"
      data-csrf="<?= h(csrf_token()) ?>"
      data-base="<?= h(base_url()) ?>">
  <div class="center muted" style="padding:80px 0"><span class="dots"><i></i><i></i><i></i></span></div>
</main>
<div class="toast" id="toast" role="status" aria-live="polite"></div>
<script src="<?= h(asset_v('assets/js/qrcode.js')) ?>"></script>
<script src="<?= h(asset_v('assets/js/i18n.js')) ?>"></script>
<script src="<?= h(asset_v('assets/js/common.js')) ?>"></script>
<script src="<?= h(asset_v('assets/js/host.js')) ?>"></script>
<?php endif; ?>
</body>
</html>
