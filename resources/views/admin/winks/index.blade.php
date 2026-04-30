@extends('layouts.admin')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>😉 Управление подмигиваниями</h1>
    </div>

    <!-- Статистика -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h5>Всего</h5>
                    <h2>{{ $stats['total'] }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h5>Подтверждено</h5>
                    <h2>{{ $stats['accepted'] }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <h5>Ожидают</h5>
                    <h2>{{ $stats['pending'] }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-secondary text-white">
                <div class="card-body">
                    <h5>Проигнорировано</h5>
                    <h2>{{ $stats['ignored'] }}</h2>
                </div>
            </div>
        </div>
    </div>

    <!-- Список подмигиваний -->
    <div class="card">
        <div class="card-header">
            <h5>Список подмигиваний</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>От</th>
                        <th>Кому</th>
                        <th>Событие</th>
                        <th>Статус</th>
                        <th>Дата</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($winks as $wink)
                        <tr>
                            <td>{{ $wink->id }}</td>
                            <td>{{ $wink->fromUser->name ?? '—' }}</td>
                            <td>{{ $wink->toUser->name ?? '—' }}</td>
                            <td>{{ $wink->event->title ?? '—' }}</td>
                            <td>
                            <span class="badge {{ $wink->status_badge_class }}">
                                {{ $wink->status_name }}
                            </span>
                            </td>
                            <td>{{ $wink->created_at->format('d.m.Y H:i') }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            {{ $winks->links() }}
        </div>
    </div>
@endsection
