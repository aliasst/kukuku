@extends('layouts.admin')

@section('content')
    <div class="container-fluid px-0">
        <!-- Заголовок -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h2">Панель управления</h1>
            <div class="text-muted">
                <i class="fas fa-calendar-alt me-1"></i> {{ now()->format('d.m.Y H:i') }}
            </div>
        </div>

        <!-- Статистика в карточках -->
        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h5>Пользователи</h5>
                            <h2 class="mt-2 mb-0">{{ number_format($stats['users']) }}</h2>
                            <small class="text-muted">
                                <i class="fas fa-user-tie me-1"></i> Админов: {{ $stats['admins'] }}
                            </small>
                        </div>
                        <div class="stat-icon">
                            <i class="fas fa-users"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="stat-card">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h5>События</h5>
                            <h2 class="mt-2 mb-0">{{ number_format($stats['events']) }}</h2>
                            <small class="text-muted">
                                <i class="fas fa-calendar-week me-1"></i> Предстоит: {{ $stats['upcoming_events'] }}
                            </small>
                        </div>
                        <div class="stat-icon">
                            <i class="fas fa-calendar-alt"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="stat-card">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h5>Подарки</h5>
                            <h2 class="mt-2 mb-0">{{ number_format($stats['gifts_sent']) }}</h2>
                            <small class="text-success">
                                <i class="fas fa-check-circle me-1"></i> Получено: {{ $stats['gifts_accepted'] }}
                            </small>
                        </div>
                        <div class="stat-icon">
                            <i class="fas fa-gift"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="stat-card">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h5>Подмигивания</h5>
                            <h2 class="mt-2 mb-0">{{ number_format($stats['winks_sent']) }}</h2>
                            <small class="text-success">
                                <i class="fas fa-check-circle me-1"></i> Взаимных: {{ $stats['winks_accepted'] }}
                            </small>
                        </div>
                        <div class="stat-icon">
                            <i class="fas fa-smile-wink"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Графики -->
        <div class="row g-3 mb-4">
            <div class="col-md-8">
                <div class="chart-card">
                    <div class="card-header">
                        <h5><i class="fas fa-chart-line me-2"></i>Активность за последние 30 дней</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="activityChart" style="height: 300px;"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="chart-card">
                    <div class="card-header">
                        <h5><i class="fas fa-chart-pie me-2"></i>Пользователи по ролям</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="userRolesChart" style="height: 250px;"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-md-6">
                <div class="chart-card">
                    <div class="card-header">
                        <h5><i class="fas fa-chart-bar me-2"></i>Статусы подарков</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="giftStatusChart" style="height: 250px;"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="chart-card">
                    <div class="card-header">
                        <h5><i class="fas fa-trophy me-2"></i>Топ пользователей по подаркам</h5>
                    </div>
                    <div class="card-body">
                        @if($topUsers->count() > 0)
                            <div class="top-users-list">
                                @foreach($topUsers as $index => $user)
                                    <div class="top-user-item">
                                        <div class="top-user-rank">{{ $index + 1 }}</div>
                                        <div class="top-user-name">{{ $user->name }}</div>
                                        <div class="top-user-count">
                                            <i class="fas fa-gift text-warning"></i> {{ $user->gifts_count }}
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-muted text-center py-4">Нет данных</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Последняя активность -->
        <div class="row g-3">
            <div class="col-12">
                <div class="chart-card">
                    <div class="card-header">
                        <h5><i class="fas fa-clock me-2"></i>Последние действия</h5>
                    </div>
                    <div class="card-body">
                        <div class="activities-list">
                            @foreach($recentActivities as $activity)
                                <div class="activity-item">
                                    <div class="activity-icon bg-{{ $activity->color }}">
                                        <i class="{{ $activity->icon }}"></i>
                                    </div>
                                    <div class="activity-content">
                                        <div class="activity-message">{{ $activity->message }}</div>
                                        <div class="activity-time">{{ $activity->time->diffForHumans() }}</div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Chart.js -->
{{--    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>--}}
    <script src="{{ asset('foradmin/chart.umd.min.js') }}"/></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // График активности
            const ctx1 = document.getElementById('activityChart').getContext('2d');
            new Chart(ctx1, {
                type: 'line',
                data: {
                    labels: @json($dates),
                    datasets: [
                        {
                            label: 'Регистрации',
                            data: @json($userData),
                            borderColor: '#818cf8',
                            backgroundColor: 'rgba(129, 140, 248, 0.1)',
                            tension: 0.4,
                            fill: true
                        },
                        {
                            label: 'Подарки',
                            data: @json($giftData),
                            borderColor: '#fbbf24',
                            backgroundColor: 'rgba(251, 191, 36, 0.1)',
                            tension: 0.4,
                            fill: true
                        },
                        {
                            label: 'Подмигивания',
                            data: @json($winkData),
                            borderColor: '#4ade80',
                            backgroundColor: 'rgba(74, 222, 128, 0.1)',
                            tension: 0.4,
                            fill: true
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: { color: '#94a3b8' }
                        }
                    },
                    scales: {
                        y: { grid: { color: '#2d2d44' }, ticks: { color: '#94a3b8' } },
                        x: { grid: { color: '#2d2d44' }, ticks: { color: '#94a3b8' } }
                    }
                }
            });

            // Круговая диаграмма ролей
            const ctx2 = document.getElementById('userRolesChart').getContext('2d');
            new Chart(ctx2, {
                type: 'doughnut',
                data: {
                    labels: @json(array_keys($userRoles)),
                    datasets: [{
                        data: @json(array_values($userRoles)),
                        backgroundColor: ['#818cf8', '#fbbf24', '#4ade80'],
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'bottom', labels: { color: '#94a3b8' } }
                    }
                }
            });

            // Круговая диаграмма статусов подарков
            const ctx3 = document.getElementById('giftStatusChart').getContext('2d');
            new Chart(ctx3, {
                type: 'pie',
                data: {
                    labels: @json(array_keys($giftStatuses)),
                    datasets: [{
                        data: @json(array_values($giftStatuses)),
                        backgroundColor: ['#4ade80', '#fbbf24', '#f87171'],
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'bottom', labels: { color: '#94a3b8' } }
                    }
                }
            });
        });
    </script>

    <style>
        .stat-card {
            background: #0f0f1a;
            border: 1px solid #2d2d44;
            border-radius: 15px;
            padding: 20px;
            transition: all 0.3s;
        }

        .stat-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.2);
        }

        .stat-card h5 {
            color: #94a3b8;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 5px;
        }

        .stat-card h2 {
            color: #e2e8f0;
            font-size: 32px;
            font-weight: 700;
        }

        .stat-icon {
            width: 48px;
            height: 48px;
            background: rgba(129, 140, 248, 0.1);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            color: #818cf8;
        }

        .chart-card {
            background: #0f0f1a;
            border: 1px solid #2d2d44;
            border-radius: 15px;
            overflow: hidden;
        }

        .chart-card .card-header {
            background: rgba(0,0,0,0.2);
            padding: 15px 20px;
            border-bottom: 1px solid #2d2d44;
        }

        .chart-card .card-header h5 {
            margin: 0;
            font-size: 14px;
            color: #cbd5e1;
        }

        .chart-card .card-body {
            padding: 20px;
        }

        .top-users-list {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .top-user-item {
            display: flex;
            align-items: center;
            gap: 15px;
            padding: 10px;
            background: rgba(255,255,255,0.03);
            border-radius: 10px;
        }

        .top-user-rank {
            width: 30px;
            height: 30px;
            background: rgba(129, 140, 248, 0.2);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            color: #818cf8;
        }

        .top-user-name {
            flex: 1;
            color: #e2e8f0;
            font-weight: 500;
        }

        .top-user-count {
            color: #fbbf24;
            font-weight: 600;
        }

        .activities-list {
            display: flex;
            flex-direction: column;
            gap: 12px;
            max-height: 400px;
            overflow-y: auto;
        }

        .activity-item {
            display: flex;
            align-items: center;
            gap: 15px;
            padding: 10px;
            background: rgba(255,255,255,0.02);
            border-radius: 10px;
            transition: all 0.2s;
        }

        .activity-item:hover {
            background: rgba(255,255,255,0.05);
        }

        .activity-icon {
            width: 40px;
            height: 40px;
            background: rgba(129, 140, 248, 0.1);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
        }

        .bg-success { background: rgba(74, 222, 128, 0.15); color: #4ade80; }
        .bg-primary { background: rgba(129, 140, 248, 0.15); color: #818cf8; }
        .bg-warning { background: rgba(251, 191, 36, 0.15); color: #fbbf24; }

        .activity-content {
            flex: 1;
        }

        .activity-message {
            color: #cbd5e1;
            font-size: 14px;
            margin-bottom: 4px;
        }

        .activity-time {
            color: #64748b;
            font-size: 11px;
        }

        /* Адаптив */
        @media (max-width: 768px) {
            .stat-card h2 {
                font-size: 24px;
            }

            .stat-icon {
                width: 40px;
                height: 40px;
                font-size: 20px;
            }
        }
    </style>
@endsection
