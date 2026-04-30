@extends('layouts.admin')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>🎁 Управление подарками</h1>
    </div>

    <!-- Статистика -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h5>Всего отправлено</h5>
                    <h2>{{ $giftStats['total_sent'] }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h5>Получено</h5>
                    <h2>{{ $giftStats['total_accepted'] }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <h5>Ожидают</h5>
                    <h2>{{ $giftStats['total_pending'] }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-secondary text-white">
                <div class="card-body">
                    <h5>Отклонено</h5>
                    <h2>{{ $giftStats['total_ignored'] }}</h2>
                </div>
            </div>
        </div>
    </div>

    <!-- Список подарков -->
    <div class="card">
        <div class="card-header">
            <h5>Список подарков</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>Иконка</th>
                        <th>Название</th>
                        <th>Цена</th>
                        <th>Сортировка</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($gifts as $gift)
                        <tr>
                            <td>{{ $gift->id }}</td>
                            <td style="font-size: 24px;"><img src="{{ asset($gift->icon) }}" alt=""></td>
                            <td>{{ $gift->name }}</td>
                            <td>{{ number_format($gift->price, 0, ',', ' ') }} ₽</td>
                            <td>{{ $gift->sort_order }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Последние подарки -->
    <div class="card mt-4">
        <div class="card-header">
            <h5>Последние подарки</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                    <tr>
                        <th>Подарок</th>
                        <th>От</th>
                        <th>Кому</th>
                        <th>Событие</th>
                        <th>Статус</th>
                        <th>Дата</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($recentGifts as $gift)
                        <tr>
                            <td>{{ $gift->gift->name ?? '—' }}</td>
                            <td>{{ $gift->fromUser->name ?? '—' }}</td>
                            <td>{{ $gift->toUser->name ?? '—' }}</td>
                            <td>{{ $gift->event->title ?? '—' }}</td>
                            <td>
                            <span class="badge {{ $gift->status_badge_class }}">
                                {{ $gift->status_name }}
                            </span>
                            </td>
                            <td>{{ $gift->created_at->format('d.m.Y H:i') }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            {{ $recentGifts->links() }}
        </div>
    </div>
@endsection
