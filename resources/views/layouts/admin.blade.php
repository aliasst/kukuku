<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Админ-панель - {{ config('app.name') }}</title>

    <link rel="stylesheet" href="{{ asset('foradmin/bootstrap.min.css') }}"/>
    <link rel="stylesheet" href="{{ asset('foradmin/fontawesome/css/all.min.css') }}"/>


    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background-color: #f0f2f5;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        /* Sidebar */
        .sidebar {
            min-height: 100vh;
            background: linear-gradient(135deg, #1e1e2f 0%, #2d2d44 100%);
            color: #fff;
            box-shadow: 2px 0 10px rgba(0,0,0,0.1);
        }

        .sidebar h4 {
            font-size: 1.3rem;
            font-weight: 600;
            letter-spacing: 1px;
        }

        .sidebar .nav-link {
            color: rgba(255,255,255,0.7);
            padding: 10px 18px;
            margin: 4px 0;
            border-radius: 10px;
            transition: all 0.3s;
            font-size: 14px;
        }

        .sidebar .nav-link:hover {
            background: rgba(255,255,255,0.1);
            color: white;
            transform: translateX(5px);
        }

        .sidebar .nav-link.active {
            background: rgba(255,255,255,0.15);
            color: white;
        }

        .sidebar .nav-link i {
            width: 24px;
            margin-right: 10px;
            font-size: 16px;
        }

        /* Main content */
        .main-content {
            padding: 25px;
        }

        /* Карточки статистики */
        .stat-card {
            border-radius: 12px;
            border: none;
            transition: transform 0.2s, box-shadow 0.2s;
            cursor: pointer;
        }

        .stat-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }

        .stat-card .card-body {
            padding: 1.25rem;
        }

        .stat-card h5 {
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            opacity: 0.9;
            margin-bottom: 10px;
        }

        .stat-card h2 {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 0;
        }

        /* Таблицы */
        .table-responsive {
            overflow-x: auto;
        }

        .table {
            font-size: 14px;
            margin-bottom: 0;
        }

        .table th {
            background-color: #f8f9fa;
            font-weight: 600;
            border-bottom: 2px solid #e9ecef;
            padding: 12px 8px;
        }

        .table td {
            padding: 10px 8px;
            vertical-align: middle;
        }

        /* Пагинация - исправляем размер */
        .pagination {
            margin-top: 20px;
            display: flex;
            justify-content: center;
            gap: 5px;
            flex-wrap: wrap;
        }

        .pagination .page-link {
            padding: 6px 12px;
            font-size: 14px;
            border-radius: 8px;
            color: #4a5568;
            border: 1px solid #e2e8f0;
            background: white;
        }

        .pagination .page-item.active .page-link {
            background: #2d2d44 ;
            border-color: #2d2d44 ;
            color: white;
        }

        .pagination .page-link:hover {
            background: #f7fafc;
            border-color: #cbd5e0;
            transform: translateY(-1px);
        }

        .pagination .page-item.disabled .page-link {
            color: #a0aec0;
            background: #edf2f7;
        }

        /* Кнопки */
        .btn-sm {
            padding: 4px 10px;
            font-size: 12px;
            border-radius: 6px;
        }

        .btn-group-sm .btn {
            padding: 4px 8px;
            font-size: 12px;
        }

        /* Баджи */
        .badge {
            font-size: 11px;
            padding: 4px 8px;
            border-radius: 20px;
            font-weight: 500;
        }

        /* Формы */
        .form-control, .form-select {
            border-radius: 8px;
            border: 1px solid #e2e8f0;
            padding: 8px 12px;
            font-size: 14px;
        }

        .form-control:focus, .form-select:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102,126,234,0.1);
        }

        /* Карточки */
        .card {
            border-radius: 12px;
            border: none;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
            margin-bottom: 20px;
        }

        .card-header {
            background: white;
            border-bottom: 1px solid #edf2f7;
            padding: 15px 20px;
            font-weight: 600;
            border-radius: 12px 12px 0 0;
        }

        .card-body {
            padding: 20px;
        }

        /* Адаптив */
        @media (max-width: 768px) {
            .sidebar {
                min-height: auto;
                padding: 10px;
            }

            .main-content {
                padding: 15px;
            }

            .stat-card h2 {
                font-size: 1.5rem;
            }

            .table {
                font-size: 12px;
            }

            .pagination .page-link {
                padding: 4px 8px;
                font-size: 12px;
            }
        }
    </style>
</head>
<body>
<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-md-3 col-lg-2 px-0">
            <div class="sidebar p-3">
                <h4 class="text-center mb-4">⚡ Admin Panel</h4>
                <nav class="nav flex-column">
                    <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"
                       href="{{ route('admin.dashboard') }}">
                        <i class="fas fa-tachometer-alt"></i> Главная
                    </a>
                    <a class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}"
                       href="{{ route('admin.users.index') }}">
                        <i class="fas fa-users"></i> Пользователи
                    </a>
                    <a class="nav-link {{ request()->routeIs('admin.events.*') ? 'active' : '' }}"
                       href="{{ route('admin.events.index') }}">
                        <i class="fas fa-calendar-alt"></i> События
                    </a>
                    <a class="nav-link {{ request()->routeIs('admin.gifts.*') ? 'active' : '' }}"
                       href="{{ route('admin.gifts.index') }}">
                        <i class="fas fa-gift"></i> Подарки
                    </a>
                    <a class="nav-link {{ request()->routeIs('admin.winks.*') ? 'active' : '' }}"
                       href="{{ route('admin.winks.index') }}">
                        <i class="fas fa-smile-wink"></i> Подмигивания
                    </a>
                </nav>

                <hr class="bg-light mt-4">

                <a class="nav-link" href="{{ route('home') }}">
                    <i class="fas fa-arrow-left"></i> На сайт
                </a>
                <a class="nav-link" href="{{ route('logout') }}"
                   onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    <i class="fas fa-sign-out-alt"></i> Выход
                </a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                    @csrf
                </form>
            </div>
        </div>

        <!-- Main content -->
        <div class="col-md-9 col-lg-10 main-content">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @yield('content')
        </div>
    </div>
</div>


<script src="{{ asset('foradmin/bootstrap.bundle.min.js') }}"></script>
</body>
</html>
