<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Вход в админ-панель - {{ config('app.name') }}</title>

    <link rel="stylesheet" href="{{ asset('foradmin/bootstrap.min.css') }}"/>
    <link rel="stylesheet" href="{{ asset('foradmin/fontawesome/css/all.min.css') }}"/>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background: #fff;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', 'Inter', Tahoma, Geneva, Verdana, sans-serif;
            padding: 20px;
        }

        .login-card {
            background: linear-gradient(180deg, #f6a23c 98%);
            border-radius: 20px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.8);
            overflow: hidden;
            width: 100%;
            max-width: 450px;
            animation: fadeInUp 0.5s ease;
            border: 1px solid rgba(255, 255, 255, 0.08);
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .login-header {
            background: linear-gradient(180deg, #f6a23c 98%);
            padding: 35px 30px;
            text-align: center;
            color: #e2e8f0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        }

        .login-header h1 {
            font-size: 28px;
            margin-bottom: 10px;
            font-weight: 600;
            letter-spacing: 1px;
            color: #e2e8f0;
        }

        .login-header p {
            font-size: 14px;
            opacity: 0.6;
            margin-bottom: 0;
            color: #060000;
        }

        .logo-icon {
            font-size: 52px;
            margin-bottom: 15px;
            color:  rgb(245, 75, 16);


        }

        .login-body {
            padding: 35px 30px;
        }

        .form-control {
            border-radius: 10px;
            border: 0;
            background: #ffc074;
            padding: 12px 15px;
            font-size: 14px;
            color: #060000;
            transition: all 0.3s;
        }

        .form-control:focus {
            border-color: #060000;
            background: #ffc074;
            box-shadow: 0 0 0 3px rgba(129, 140, 248, 0.15);
            color: #060000;
        }

        .form-control::placeholder {
            color: #060000;
        }

        .input-group-text {
            background: #ffc074;
            border: 0;
            border-right: none;
            border-radius: 10px 0 0 10px;
            color: #060000;
        }

        .input-group .form-control {
            border-left: none;
            border-radius: 0 10px 10px 0;
        }

        .btn-login {
            background: linear-gradient(90.00deg, rgb(247, 90, 34), rgb(255, 98, 43) 50%, rgb(245, 75, 16) 96%);
            border: 0;
            padding: 12px;
            font-size: 16px;
            font-weight: 600;
            border-radius: 10px;
            width: 100%;
            color: #cbd5e1;
            transition: all 0.3s;
        }

        .btn-login:hover {
            background: linear-gradient(90.00deg, rgb(247, 90, 34), rgb(255, 98, 43) 50%, rgb(245, 75, 16) 96%);
            border-color: #060000;
            color: #e2e8f0;
            transform: translateY(-1px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
        }

        .form-check-input {
            background-color: #ffc074;
            border:0;
            border-color: #2d2d44;
        }

        .form-check-input:checked {
            background-color: #060000;
            border-color: #060000;
        }

        .form-check-label {
            color: #060000;
            font-size: 13px;
        }

        .alert {
            border-radius: 10px;
            font-size: 13px;
            background: #1a1a2e;
            border: 1px solid #2d2d44;
            color: #fca5a5;
        }

        .alert-danger {
            background: rgba(220, 38, 38, 0.08);
            border-left: 3px solid #ef4444;
            color: #fecaca;
        }

        .back-to-site {
            text-align: center;
            margin-top: 20px;
        }

        .back-to-site a {
            color: #060000;
            text-decoration: none;
            font-size: 13px;
            transition: all 0.3s;
            opacity: 0.7;
        }

        .back-to-site a:hover {
            color: #a5b4fc;
            opacity: 1;
            text-decoration: underline;
        }

        .forgot-link {
            color: #060000;
            font-size: 12px;
            text-decoration: none;
            opacity: 0.7;
        }

        .forgot-link:hover {
            color: #a5b4fc;
            opacity: 1;
            text-decoration: underline;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            color: #060000;
            font-size: 12px;
            font-weight: 500;
            margin-bottom: 8px;
            display: block;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }

        /* Стили для иконок внутри полей */
        .input-group-text i {
            color: #060000;
            opacity: 0.7;
        }

        input:-webkit-autofill,
        input:-webkit-autofill:focus {
            transition: background-color 0s 600000s, color 0s 600000s;
        }

        hr {
            border-color: #2d2d44;
            margin: 20px 0;
        }
    </style>
</head>
<body>
<div class="login-card">
    <div class="login-header">
        <div class="logo-icon">
            <i class="fas fa-shield-alt"></i>
        </div>
        <h1>Админ панель</h1>
        <p>Войдите, чтобы продолжить</p>
    </div>

    <div class="login-body">
        @if($errors->any())
            <div class="alert alert-danger mb-4">
                <i class="fas fa-exclamation-triangle me-2"></i>
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('admin.login.submit') }}">
            @csrf

            <div class="form-group">
                <label>Email</label>
                <div class="input-group">
                        <span class="input-group-text">
                            <i class="fas fa-envelope"></i>
                        </span>
                    <input type="email" name="email" class="form-control"
                           value="{{ old('email') }}" required autofocus
                           placeholder="admin@example.com">
                </div>
            </div>

            <div class="form-group">
                <label>Пароль</label>
                <div class="input-group">
                        <span class="input-group-text">
                            <i class="fas fa-lock"></i>
                        </span>
                    <input type="password" name="password" class="form-control"
                           required placeholder="••••••••">
                </div>
            </div>

            <div class="form-group d-flex justify-content-between align-items-center">
                <div class="form-check">
                    <input type="checkbox" name="remember" class="form-check-input" id="remember">
                    <label class="form-check-label" for="remember">Запомнить меня</label>
                </div>
                <a href="{{ route('password.request') }}" class="forgot-link">
                    Забыли пароль?
                </a>
            </div>

            <button type="submit" class="btn-login mt-2">
                <i class="fas fa-sign-in-alt me-2"></i> Войти в панель
            </button>
        </form>

        <hr>

        <div class="back-to-site">
            <a href="{{ route('home') }}">
                <i class="fas fa-arrow-left me-1"></i> Вернуться на сайт
            </a>
        </div>
    </div>
</div>
</body>
</html>
