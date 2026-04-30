@extends('layouts.admin')

@section('content')
    <h1 class="mb-4">Панель управления</h1>

    <div class="row">
        <div class="col-md-3 mb-3">
            <div class="card stat-card bg-primary text-white">
                <div class="card-body">
                    <h5 class="card-title">Пользователи</h5>
                    <h2 class="display-4">{{ $stats['users'] }}</h2>
                    <p>Админов: {{ $stats['admins'] }} | Редакторов: {{ $stats['editors'] }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card stat-card bg-success text-white">
                <div class="card-body">
                    <h5 class="card-title">События</h5>
                    <h2 class="display-4">{{ $stats['events'] }}</h2>
                    <p>Будущих: {{ $stats['upcoming_events'] }} | Прошедших: {{ $stats['past_events'] }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card stat-card bg-warning text-white">
                <div class="card-body">
                    <h5 class="card-title">Подарки</h5>
                    <h2 class="display-4">{{ $stats['gifts_sent'] }}</h2>
                    <p>Отправлено: {{ $stats['gifts_sent'] }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card stat-card bg-info text-white">
                <div class="card-body">
                    <h5 class="card-title">Подмигивания</h5>
                    <h2 class="display-4">{{ $stats['winks_sent'] }}</h2>
                    <p>Отправлено: {{ $stats['winks_sent'] }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5>Последние пользователи</h5>
                </div>
                <div class="card-body">
                    <table class="table">
                        <thead>
                        <tr><th>Имя</th><th>Email</th><th>Роль</th><th>Дата</th></tr>
                        </thead>
                        <tbody>
                        @foreach($recentUsers as $user)
                            <tr>
                                <td>{{ $user->name }}</td>
                                <td>{{ $user->email }}</td>
                                <td><span class="badge {{ $user->role_badge_class }}">{{ $user->role_name }}</span></td>
                                <td>{{ $user->created_at->format('d.m.Y') }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5>Последние события</h5>
                </div>
                <div class="card-body">
                    <table class="table">
                        <thead><tr><th>Название</th><th>Дата</th><th>Участников</th></tr></thead>
                        <tbody>
                        @foreach($recentEvents as $event)
                            <tr>
                                <td>{{ $event->title }}</td>
                                <td>{{ $event->formatted_date }}</td>
                                <td>{{ $event->users_count }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
