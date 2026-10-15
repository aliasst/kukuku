<?php
declare(strict_types=1);
error_reporting(E_ALL);
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');

require __DIR__ . '/inc/bootstrap.php';
require __DIR__ . '/inc/game.php';
start_session();

// ------------------------------------------------------------------ login / logout
if (isset($_GET['logout'])) {
    $_SESSION = [];
    session_destroy();
    header('Location: admin.php');
    exit;
}
$loginError = '';
if (!is_admin() && ($_SERVER['REQUEST_METHOD'] ?? '') === 'POST' && isset($_POST['password'])) {
    if (admin_login((string) $_POST['password'])) {
        header('Location: admin.php');
        exit;
    }
    usleep(700000);
    $loginError = 'Неверный пароль';
}

if (!is_admin()) {
    page_head('Вход');
    ?>
    <main class="phone">
      <form class="panel stack" method="post" style="margin-top:40px">
        <h1 class="h-m">Админка KuKu'Ku</h1>
        <input class="input" type="password" name="password" placeholder="Пароль" autofocus required>
        <?php if ($loginError): ?><div class="error"><?= h($loginError) ?></div><?php endif; ?>
        <button class="btn btn--primary btn--block" type="submit">Войти</button>
      </form>
    </main>
    <?php
    page_foot();
    exit;
}

// ------------------------------------------------------------------ CSV export
if (($_GET['export'] ?? '') === 'leads') {
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="kukuku-leads-' . date('Y-m-d') . '.csv"');
    $out = fopen('php://output', 'w');
    fwrite($out, "\xEF\xBB\xBF");
    fputcsv($out, ['Дата', 'Стол', 'Код', 'Карточка', 'Имя', 'Телефон', 'Email', 'Instagram', 'Язык', 'Согласие']);
    $rows = q('SELECT l.*, g.code FROM leads l LEFT JOIN games g ON g.id = l.game_id ORDER BY l.id DESC')->fetchAll();
    foreach ($rows as $r) {
        fputcsv($out, [date('Y-m-d H:i', (int) $r['created_at']), $r['game_id'], $r['code'], $r['card_title'], $r['name'],
            $r['phone'], $r['email'], $r['instagram'] ? '@' . $r['instagram'] : '', $r['lang'], $r['consent'] ? 'да' : 'нет']);
    }
    exit;
}

// ------------------------------------------------------------------ actions
$flash = '';
$flashErr = false;
if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
    if (!check_csrf($_POST['csrf'] ?? null)) {
        $flash = 'Сессия устарела, обновите страницу.';
        $flashErr = true;
    } else {
        try {
            $flash = handle_post();
        } catch (Throwable $e) {
            $flash = $e->getMessage();
            $flashErr = true;
        }
    }
}

function handle_post(): string
{
    $act = (string) ($_POST['act'] ?? '');
    if ($act === 'save_track') {
        $id = (int) ($_POST['id'] ?? 0);
        $title = trim((string) ($_POST['title'] ?? ''));
        if ($title === '') {
            throw new RuntimeException('Укажите название трека');
        }
        $data = [
            'title'      => mb_substr($title, 0, 120),
            'apple_url'  => trim((string) ($_POST['apple_url'] ?? '')) ?: null,
            'start_sec'  => max(0, (int) ($_POST['start_sec'] ?? 0)),
            'sort_order' => (int) ($_POST['sort_order'] ?? 0),
            'active'     => !empty($_POST['active']) ? 1 : 0,
        ];
        if ($id) {
            q('UPDATE tracks SET title = ?, apple_url = ?, start_sec = ?, sort_order = ?, active = ? WHERE id = ?',
                [$data['title'], $data['apple_url'], $data['start_sec'], $data['sort_order'], $data['active'], $id]);
        } else {
            q('INSERT INTO tracks (title, apple_url, start_sec, sort_order, active, created_at) VALUES (?, ?, ?, ?, ?, ?)',
                [$data['title'], $data['apple_url'], $data['start_sec'], $data['sort_order'], $data['active'], time()]);
            $id = (int) db()->lastInsertId();
        }
        $audio = save_upload('audio', $id, 'audio', ['mp3' => ['audio/mpeg', 'audio/mp3'], 'm4a' => ['audio/mp4', 'audio/x-m4a'],
            'wav' => ['audio/wav', 'audio/x-wav'], 'ogg' => ['audio/ogg']]);
        if ($audio) {
            replace_file($id, 'audio_file', $audio);
        }
        $cover = save_upload('cover', $id, 'covers', ['jpg' => ['image/jpeg'], 'jpeg' => ['image/jpeg'], 'png' => ['image/png'], 'webp' => ['image/webp']]);
        if ($cover) {
            replace_file($id, 'cover_file', $cover);
        }
        return 'Трек сохранён';
    }
    if ($act === 'delete_track') {
        $id = (int) ($_POST['id'] ?? 0);
        $t = q('SELECT * FROM tracks WHERE id = ?', [$id])->fetch();
        if ($t) {
            foreach (['audio_file', 'cover_file'] as $f) {
                if ($t[$f]) {
                    @unlink(APP_ROOT . '/uploads/' . $t[$f]);
                }
            }
            q('DELETE FROM tracks WHERE id = ?', [$id]);
        }
        return 'Трек удалён';
    }
    if ($act === 'finish_game') {
        $g = game_get((int) ($_POST['id'] ?? 0));
        if ($g && $g['phase'] !== 'finished') {
            host_finish($g);
        }
        return 'Стол закрыт';
    }
    if ($act === 'delete_lead') {
        q('DELETE FROM leads WHERE id = ?', [(int) ($_POST['id'] ?? 0)]);
        return 'Контакт удалён';
    }
    if ($act === 'test_ai') {
        $r = ai_summary('Test track', ['Feels like summer evening by the sea', 'Хочется танцевать', 'Μου θυμίζει διακοπές']);
        $_SESSION['ai_test'] = $r ? json_encode($r, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) : 'AI не ответил. Проверьте ключ ai_api_key в config.php и логи сервера.';
        return 'Проверка AI выполнена';
    }
    return '';
}

function save_upload(string $field, int $id, string $dir, array $allowed): ?string
{
    if (empty($_FILES[$field]) || ($_FILES[$field]['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
        return null;
    }
    $f = $_FILES[$field];
    if ($f['error'] !== UPLOAD_ERR_OK) {
        throw new RuntimeException('Файл не загрузился (код ' . $f['error'] . '). Возможно, он больше лимита upload_max_filesize на сервере.');
    }
    $ext = strtolower(pathinfo((string) $f['name'], PATHINFO_EXTENSION));
    if (!isset($allowed[$ext])) {
        throw new RuntimeException('Недопустимый формат: .' . $ext . '. Можно: ' . implode(', ', array_keys($allowed)));
    }
    $mime = function_exists('finfo_open') ? (string) finfo_file(finfo_open(FILEINFO_MIME_TYPE), $f['tmp_name']) : '';
    if ($mime !== '' && !in_array($mime, $allowed[$ext], true) && $mime !== 'application/octet-stream') {
        throw new RuntimeException('Файл не похож на .' . $ext . ' (' . $mime . ')');
    }
    $name = $dir . '/' . $id . '-' . bin2hex(random_bytes(4)) . '.' . $ext;
    if (!move_uploaded_file($f['tmp_name'], APP_ROOT . '/uploads/' . $name)) {
        throw new RuntimeException('Не удалось сохранить файл. Проверьте права на папку uploads/' . $dir);
    }
    return $name;
}

function replace_file(int $id, string $col, string $new): void
{
    $old = q("SELECT $col FROM tracks WHERE id = ?", [$id])->fetchColumn();
    if ($old) {
        @unlink(APP_ROOT . '/uploads/' . $old);
    }
    q("UPDATE tracks SET $col = ? WHERE id = ?", [$new, $id]);
}

// ------------------------------------------------------------------ page
$tab = (string) ($_GET['tab'] ?? 'tracks');
$csrf = csrf_token();

function page_head(string $title): void
{
    ?><!doctype html>
<html lang="ru">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="robots" content="noindex">
<title><?= h($title) ?> · KuKu'Ku</title>
<link rel="stylesheet" href="<?= h(asset_v('assets/css/fonts.css')) ?>">
<link rel="stylesheet" href="<?= h(asset_v('assets/css/app.css')) ?>">
</head>
<body>
<header class="top">
  <a class="logo" href="admin.php">KuKu'Ku</a>
  <div class="top__right">
    <?php if (is_admin()): ?>
      <a class="btn btn--primary btn--s" href="host.php">Планшет стола</a>
      <a class="btn btn--ghost btn--s" href="admin.php?logout=1">Выйти</a>
    <?php endif; ?>
  </div>
</header>
<?php
}

function page_foot(): void
{
    echo '</body></html>';
}

page_head('Админка');
?>
<main class="admin stack-l">
  <nav class="tabs">
    <a href="?tab=tracks" class="<?= $tab === 'tracks' ? 'on' : '' ?>">Треки</a>
    <a href="?tab=games" class="<?= $tab === 'games' ? 'on' : '' ?>">Столы</a>
    <a href="?tab=leads" class="<?= $tab === 'leads' ? 'on' : '' ?>">Контакты</a>
    <a href="?tab=settings" class="<?= $tab === 'settings' ? 'on' : '' ?>">Настройки</a>
  </nav>

  <?php if ($flash): ?><div class="flash <?= $flashErr ? 'flash--err' : '' ?>"><?= h($flash) ?></div><?php endif; ?>

<?php if ($tab === 'tracks'):
    $edit = isset($_GET['edit']) ? q('SELECT * FROM tracks WHERE id = ?', [(int) $_GET['edit']])->fetch() : null;
    $tracks = q('SELECT * FROM tracks ORDER BY sort_order, id')->fetchAll();
    $activeCount = count(array_filter($tracks, fn($t) => $t['active']));
?>
  <section class="panel stack">
    <h2 class="h-m"><?= $edit ? 'Редактировать трек' : 'Добавить трек' ?></h2>
    <form method="post" enctype="multipart/form-data" class="form-grid">
      <input type="hidden" name="csrf" value="<?= h($csrf) ?>">
      <input type="hidden" name="act" value="save_track">
      <input type="hidden" name="id" value="<?= (int) ($edit['id'] ?? 0) ?>">
      <label class="field full"><span>Название трека (это и есть карточка гостя)</span>
        <input class="input" name="title" required maxlength="120" value="<?= h($edit['title'] ?? '') ?>"></label>
      <label class="field"><span>Аудио (mp3, m4a, wav, ogg)<?= !empty($edit['audio_file']) ? ' — загружено, можно заменить' : '' ?></span>
        <input class="input" type="file" name="audio" accept=".mp3,.m4a,.wav,.ogg,audio/*"></label>
      <label class="field"><span>Обложка (jpg, png, webp, квадрат)<?= !empty($edit['cover_file']) ? ' — загружена, можно заменить' : '' ?></span>
        <input class="input" type="file" name="cover" accept=".jpg,.jpeg,.png,.webp,image/*"></label>
      <label class="field"><span>Начать с секунды (например, с припева)</span>
        <input class="input" type="number" min="0" name="start_sec" value="<?= (int) ($edit['start_sec'] ?? 0) ?>"></label>
      <label class="field"><span>Порядок в игре</span>
        <input class="input" type="number" name="sort_order" value="<?= (int) ($edit['sort_order'] ?? (count($tracks) + 1)) ?>"></label>
      <label class="field full"><span>Ссылка на трек в Apple Music (необязательно)</span>
        <input class="input" name="apple_url" type="url" value="<?= h($edit['apple_url'] ?? '') ?>"></label>
      <label class="check full"><input type="checkbox" name="active" value="1" <?= ($edit === null || !empty($edit['active'])) ? 'checked' : '' ?>><span>Участвует в игре</span></label>
      <div class="full actions">
        <button class="btn btn--primary" type="submit">Сохранить</button>
        <?php if ($edit): ?><a class="btn btn--ghost" href="admin.php">Отмена</a><?php endif; ?>
      </div>
    </form>
  </section>

  <section class="panel stack">
    <h2 class="h-m">Треки</h2>
    <p class="muted" style="margin:0">В игру попадают первые <?= (int) cfg('max_tracks', 10) ?> активных треков по порядку. Сейчас активных: <?= $activeCount ?>.</p>
    <div class="table-wrap"><table class="list">
      <tr><th></th><th>Название</th><th>Аудио</th><th>Старт</th><th>Порядок</th><th>Статус</th><th></th></tr>
      <?php foreach ($tracks as $t): ?>
      <tr>
        <td><?php if ($t['cover_file']): ?><img class="thumb" src="<?= h(media_url($t['cover_file'])) ?>" alt=""><?php else: ?><div class="thumb"></div><?php endif; ?></td>
        <td><b><?= h($t['title']) ?></b></td>
        <td><?php if ($t['audio_file']): ?><audio controls preload="none" src="<?= h(media_url($t['audio_file'])) ?>" style="height:32px;max-width:220px"></audio><?php else: ?><span class="pill-off">нет файла</span><?php endif; ?></td>
        <td><?= (int) $t['start_sec'] ?> с</td>
        <td><?= (int) $t['sort_order'] ?></td>
        <td><?= $t['active'] ? '<span class="pill-ok">в игре</span>' : '<span class="pill-off">выключен</span>' ?></td>
        <td style="white-space:nowrap">
          <a class="btn btn--outline btn--s" href="?tab=tracks&edit=<?= (int) $t['id'] ?>">Изменить</a>
          <form method="post" class="inline-form" onsubmit="return confirm('Удалить трек и его файлы?')">
            <input type="hidden" name="csrf" value="<?= h($csrf) ?>"><input type="hidden" name="act" value="delete_track"><input type="hidden" name="id" value="<?= (int) $t['id'] ?>">
            <button class="btn btn--ghost btn--s" type="submit">Удалить</button>
          </form>
        </td>
      </tr>
      <?php endforeach; ?>
      <?php if (!$tracks): ?><tr><td colspan="7" class="muted">Пока нет треков. Добавьте первый выше.</td></tr><?php endif; ?>
    </table></div>
  </section>

<?php elseif ($tab === 'games'):
    $games = q('SELECT g.*, (SELECT COUNT(*) FROM players p WHERE p.game_id = g.id) AS players,
                       (SELECT COUNT(*) FROM leads l WHERE l.game_id = g.id) AS leads,
                       (SELECT COUNT(*) FROM game_tracks gt WHERE gt.game_id = g.id) AS total
                FROM games g ORDER BY g.id DESC LIMIT 100')->fetchAll();
    $phaseRu = ['lobby' => 'ждёт гостей', 'listen' => 'слушают', 'answer' => 'пишут', 'summarizing' => 'AI думает', 'summary' => 'итог', 'finished' => 'закончен'];
?>
  <section class="panel stack">
    <div class="actions" style="justify-content:space-between">
      <h2 class="h-m">Столы</h2>
      <a class="btn btn--primary btn--s" href="host.php?game=0">Открыть новый стол</a>
    </div>
    <div class="table-wrap"><table class="list">
      <tr><th>#</th><th>Код</th><th>Статус</th><th>Трек</th><th>Гостей</th><th>Контактов</th><th>Создан</th><th></th></tr>
      <?php foreach ($games as $g): ?>
      <tr>
        <td><?= (int) $g['id'] ?></td>
        <td><b><?= h($g['code']) ?></b></td>
        <td><?= h($phaseRu[$g['phase']] ?? $g['phase']) ?></td>
        <td><?= $g['position'] >= 0 ? ((int) $g['position'] + 1) . ' / ' . (int) $g['total'] : '—' ?></td>
        <td><?= (int) $g['players'] ?></td>
        <td><?= (int) $g['leads'] ?></td>
        <td><?= date('d.m H:i', (int) $g['created_at']) ?></td>
        <td style="white-space:nowrap">
          <?php if ($g['phase'] !== 'finished'): ?>
            <a class="btn btn--outline btn--s" href="host.php?game=<?= (int) $g['id'] ?>">На планшет</a>
            <form method="post" class="inline-form" onsubmit="return confirm('Закрыть этот стол?')">
              <input type="hidden" name="csrf" value="<?= h($csrf) ?>"><input type="hidden" name="act" value="finish_game"><input type="hidden" name="id" value="<?= (int) $g['id'] ?>">
              <button class="btn btn--ghost btn--s" type="submit">Закрыть</button>
            </form>
          <?php endif; ?>
        </td>
      </tr>
      <?php endforeach; ?>
      <?php if (!$games): ?><tr><td colspan="8" class="muted">Столов ещё не было.</td></tr><?php endif; ?>
    </table></div>
  </section>

<?php elseif ($tab === 'leads'):
    $leads = q('SELECT l.*, g.code FROM leads l LEFT JOIN games g ON g.id = l.game_id ORDER BY l.id DESC LIMIT 500')->fetchAll();
?>
  <section class="panel stack">
    <div class="actions" style="justify-content:space-between">
      <h2 class="h-m">Контакты гостей (<?= count($leads) ?>)</h2>
      <a class="btn btn--primary btn--s" href="?export=leads">Скачать CSV</a>
    </div>
    <div class="table-wrap"><table class="list">
      <tr><th>Дата</th><th>Стол</th><th>Карточка</th><th>Имя</th><th>Телефон</th><th>Email</th><th>Instagram</th><th>Язык</th><th></th></tr>
      <?php foreach ($leads as $l): ?>
      <tr>
        <td><?= date('d.m H:i', (int) $l['created_at']) ?></td>
        <td><?= h($l['code']) ?></td>
        <td><?= h($l['card_title']) ?></td>
        <td><b><?= h($l['name']) ?></b></td>
        <td><?= h($l['phone']) ?></td>
        <td><?= h($l['email']) ?></td>
        <td><?php if ($l['instagram']): ?><a href="https://instagram.com/<?= h(rawurlencode($l['instagram'])) ?>" target="_blank" rel="noopener">@<?= h($l['instagram']) ?></a><?php endif; ?></td>
        <td><?= h(strtoupper($l['lang'])) ?></td>
        <td>
          <form method="post" class="inline-form" onsubmit="return confirm('Удалить контакт?')">
            <input type="hidden" name="csrf" value="<?= h($csrf) ?>"><input type="hidden" name="act" value="delete_lead"><input type="hidden" name="id" value="<?= (int) $l['id'] ?>">
            <button class="btn btn--ghost btn--s" type="submit">×</button>
          </form>
        </td>
      </tr>
      <?php endforeach; ?>
      <?php if (!$leads): ?><tr><td colspan="9" class="muted">Пока никто не оставил контакты.</td></tr><?php endif; ?>
    </table></div>
  </section>

<?php else:
    $aiTest = $_SESSION['ai_test'] ?? null;
    unset($_SESSION['ai_test']);
?>
  <section class="panel stack">
    <h2 class="h-m">Настройки</h2>
    <p class="muted" style="margin:0">Настройки меняются в файле <b>config.php</b> на сервере.</p>
    <div class="table-wrap"><table class="list">
      <tr><td>AI-ключ</td><td><?= cfg('ai_api_key') ? '<span class="pill-ok">указан</span>' : '<span class="pill-off">не указан — вместо AI-итога будут показаны цитаты гостей</span>' ?></td></tr>
      <tr><td>Модель</td><td><?= h((string) cfg('ai_model')) ?></td></tr>
      <tr><td>Треков в игре</td><td><?= (int) cfg('max_tracks') ?></td></tr>
      <tr><td>Гостей за столом</td><td>от <?= (int) cfg('min_players') ?> до <?= (int) cfg('max_players') ?></td></tr>
      <tr><td>Фрагмент трека</td><td><?= (int) cfg('listen_seconds') ?> с</td></tr>
      <tr><td>Время на комментарий</td><td><?= (int) cfg('answer_seconds') ?> с</td></tr>
      <tr><td>Удалять комментарии после игры</td><td><?= cfg('delete_answers_after_game') ? 'да' : 'нет' ?></td></tr>
      <tr><td>Ссылка на музыку</td><td><?= h((string) cfg('music_url')) ?></td></tr>
      <tr><td>Ссылка на сайт</td><td><?= h((string) cfg('site_url')) ?></td></tr>
      <tr><td>Адрес для гостей</td><td><?= h(base_url()) ?>/</td></tr>
    </table></div>
    <form method="post">
      <input type="hidden" name="csrf" value="<?= h($csrf) ?>"><input type="hidden" name="act" value="test_ai">
      <button class="btn btn--outline btn--s" type="submit">Проверить AI</button>
    </form>
    <?php if ($aiTest): ?><pre style="white-space:pre-wrap;background:#F7F1EC;padding:14px;border-radius:14px;font-size:13px"><?= h($aiTest) ?></pre><?php endif; ?>
  </section>
<?php endif; ?>
</main>
<?php page_foot();
