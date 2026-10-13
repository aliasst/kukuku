KuKu'Ku Table — лендинг игр за столом (на английском)

Файлы:
  index.html   — страница (стили и скрипт внутри)
  img/         — скриншоты игры
  signup.php   — принимает заявку из формы и дописывает её в data/signups.csv
  data/        — закрыта от просмотра из браузера (.htaccess); на nginx закройте вручную

Как поставить:
  1. Залить папку, например в https://kukuku.club/table-games/ (основной лендинг вечеринок не трогаем).
  2. Дать PHP право на запись в папку data/.
  3. Открыть страницу, отправить тестовую заявку, проверить data/signups.csv.

Что заменить после первых столов:
  - добавить реальные фото гостей за столом (с их согласия);
  - в бейдже «First tables coming soon» указать дату ближайшего стола.

Музыка FunkaFunka (блок «Music by FunkaFunka»):
  - Обложки трёх треков лежат в img/cover-*.jpg. Это оригинальные обложки FunkaFunka (авторские права KuKu'Ku): только для этого лендинга.
  - Чтобы кнопки play проигрывали треки прямо на странице, впишите в атрибут data-audio у каждого <div class="track">
    тот же путь к mp3, что у карточек музыки на главной странице kukuku.club (атрибут data-audio у .music__card).
    Пока data-audio пустой, кнопка открывает FunkaFunka в Apple Music.
  - Логотип Apple Music: скачайте официальный бейдж «Listen on Apple Music» в Apple Marketing Tools
    (https://toolbox.marketingtools.apple.com) и сохраните как img/apple-music-badge.svg.
    Пока файла нет, вместо бейджа показывается обычная кнопка «Listen on Apple Music».
