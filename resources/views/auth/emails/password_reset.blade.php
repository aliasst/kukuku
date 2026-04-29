<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Сброс пароля</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .button {
            display: inline-block;
            padding: 12px 24px;
            background-color: #3490dc;
            color: white !important;
            text-decoration: none;
            border-radius: 5px;
            margin: 20px 0;
        }
        .footer {
            margin-top: 30px;
            font-size: 12px;
            color: #999;
            text-align: center;
        }
    </style>
</head>
<body>
<h2>Сброс пароля</h2>

<p>Здравствуйте!</p>

<p>Вы получили это письмо, потому что мы получили запрос на сброс пароля для вашей учетной записи.</p>

<p>
    <a href="{{ $resetUrl }}" class="button">Сбросить пароль</a>
</p>

<p>Или скопируйте ссылку в адресную строку браузера:</p>
<p>{{ $resetUrl }}</p>

<p>Если вы не запрашивали сброс пароля, просто проигнорируйте это письмо.</p>

<p>Срок действия ссылки истекает через 60 минут.</p>

<div class="footer">
    <p>© {{ date('Y') }} {{ config('app.name') }}. Все права защищены.</p>
</div>
</body>
</html>
