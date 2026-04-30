@extends('layouts.cabinet')

@section('content')

    <section id="" class="cabinet-section">
        <div class="container">
            <div class="row">
                <div class="col-xs-12">
                    <div class="cabinet-title-section">
                        <H1>Мои подмигивания</H1>
                    </div>

{{--                        @php--}}
{{--                            $pendingCount = $receivedWinks->where('status', 'pending')->count();--}}
{{--                        @endphp--}}
{{--                        @if($pendingCount > 0)--}}
{{--                            <span class="badge bg-danger">{{ $pendingCount }}</span>--}}
{{--                        @endif--}}


                    <div class="row">
                        <!-- Полученные подмигивания -->
                        <div class="col-md-6">
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h5 class="mb-0">
                                        Полученные подмигивания
                                        <span class="badge bg-danger text-white ms-2">{{ $receivedWinks->count() }}</span>
                                    </h5>
                                </div>
                                <div class="card-body" style="max-height: 600px; overflow-y: auto;">
                                    @if($receivedWinks->count() > 0)
                                        @foreach($receivedWinks as $wink)
                                            <div class="border-bottom pb-3 mb-3 wink-item"
                                                 data-wink-id="{{ $wink->id }}">
                                                <div class="d-flex justify-content-between align-items-start">
                                                    <div class="flex-grow-1">
                                                        <div class="d-flex align-items-center mb-2">
                                                            <div class="me-2">
                                                                @if($wink->fromUser->profile && $wink->fromUser->profile->avatar)
                                                                    <img
                                                                        src="{{ Storage::url($wink->fromUser->profile->avatar) }}"
                                                                        class="rounded-circle"
                                                                        style="width: 40px; height: 40px; object-fit: cover;">
                                                                @else
{{--                                                                    <div--}}
{{--                                                                        class="rounded-circle bg-secondary text-white d-flex align-items-center justify-content-center"--}}
{{--                                                                        style="width: 40px; height: 40px; font-size: 18px;">--}}
{{--                                                                        {{ substr($wink->fromUser->name, 0, 1) }}--}}
{{--                                                                    </div>--}}
                                                                    <img
                                                                        src="{{ asset("/img/avatar.svg")}}"
                                                                        class="rounded-circle"
                                                                        style="width: 50px; height: 50px; object-fit: cover;">
                                                                @endif
                                                            </div>
                                                            <div class="p-name">
                                                                <strong>{{ $wink->fromUser->name }}</strong>
                                                                <span
                                                                    class="badge {{ $wink->status_badge_class }} ms-2">
                                                    {{ $wink->status_name }}
                                                </span>
                                                                <br>
                                                                <small class="text-muted">
                                                                    📅 {{ $wink->event->title }}
                                                                </small>
                                                            </div>
                                                        </div>
                                                        <div class="ms-4">
                                                            <small class="text-muted">
                                                                {{ $wink->created_at->format('d.m.Y H:i') }}
                                                            </small>
                                                        </div>
                                                    </div>

                                                    @if($wink->status === 'pending')
                                                        <div class="btn-group">
                                                            <button class="btn btn-sm  accept-wink"
                                                                    data-wink-id="{{ $wink->id }}"
                                                                    title="Подтвердить">
                                                                ✅
                                                            </button>
                                                            <button class="btn btn-sm ignore-wink"
                                                                    data-wink-id="{{ $wink->id }}"
                                                                    title="Игнорировать">
                                                                ❌
                                                            </button>
                                                        </div>
                                                    @endif
                                                </div>

{{--                                                @if($wink->isMutual())--}}
{{--                                                    <div class="alert alert-success mt-2 mb-0 py-1 px-2 small">--}}
{{--                                                        💕 Взаимно! Вы тоже подмигнули этому пользователю.--}}
{{--                                                    </div>--}}
{{--                                                @endif--}}
                                            </div>
                                        @endforeach
                                    @else
                                        <div class="text-center py-5 text-muted">
                                            😕 Нет полученных подмигиваний
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Отправленные подмигивания -->
                        <div class="col-md-6">
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h5 class="mb-0">
                                        Отправленные подмигивания
                                        <span class="badge bg-danger ms-2">{{ $sentWinks->count() }}</span>
                                    </h5>
                                </div>
                                <div class="card-body" style="max-height: 600px; overflow-y: auto;">
                                    @if($sentWinks->count() > 0)
                                        @foreach($sentWinks as $wink)
                                            <div class="border-bottom pb-3 mb-3">
                                                <div class="d-flex justify-content-between align-items-start">
                                                    <div class="flex-grow-1">
                                                        <div class="d-flex align-items-center mb-2">
                                                            <div class="me-2">
                                                                @if($wink->toUser->profile && $wink->toUser->profile->avatar)
                                                                    <img
                                                                        src="{{ Storage::url($wink->toUser->profile->avatar) }}"
                                                                        class="rounded-circle"
                                                                        style="width: 40px; height: 40px; object-fit: cover;">
                                                                @else
                                                                    <img
                                                                        src="{{ asset("/img/avatar.svg")}}"
                                                                        class="rounded-circle"
                                                                        style="width: 50px; height: 50px; object-fit: cover;">
                                                                @endif
                                                            </div>
                                                            <div class="p-name">
                                                                <strong>{{ $wink->toUser->name }}</strong>
                                                                <span
                                                                    class="badge {{ $wink->status_badge_class }} ms-2">
                                                    {{ $wink->status_name }}
                                                </span>
                                                                <br>
                                                                <small class="text-muted">
                                                                    📅 {{ $wink->event->title }}
                                                                </small>
                                                            </div>
                                                        </div>
                                                        <div class="ms-4">
                                                            <small class="text-muted">
                                                                {{ $wink->created_at->format('d.m.Y H:i') }}
                                                            </small>
                                                        </div>
                                                    </div>
                                                </div>

{{--                                                @if($wink->isMutual())--}}
{{--                                                    <div class="alert alert-success mt-2 mb-0 py-1 px-2 small">--}}
{{--                                                        💕 Взаимно! Пользователь тоже подмигнул вам.--}}
{{--                                                    </div>--}}
{{--                                                @endif--}}
                                            </div>
                                        @endforeach
                                    @else
                                        <div class="text-center py-5 text-muted">
                                            😕 Нет отправленных подмигиваний
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                // Подтверждение подмигивания
                document.querySelectorAll('.accept-wink').forEach(btn => {
                    btn.addEventListener('click', async function () {
                        const winkId = this.dataset.winkId;
                        const row = this.closest('.wink-item');

                        // if (!confirm('Подтвердить подмигивание? Это действие нельзя отменить.')) return;

                        try {
                            const response = await fetch(`/cabinet/wink/${winkId}/accept`, {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                    'Accept': 'application/json'
                                }
                            });

                            const result = await response.json();

                            if (result.success) {
                                // Обновляем интерфейс
                                const statusBadge = row.querySelector('.badge');
                                if (statusBadge) {
                                    statusBadge.classList.remove('bg-warning');
                                    statusBadge.classList.add('bg-success');
                                    statusBadge.textContent = 'Подтверждено';
                                }

                                // Удаляем кнопки действий
                                const btnGroup = row.querySelector('.btn-group');
                                if (btnGroup) btnGroup.remove();

                                // Показываем уведомление
                                showNotification(result.message, 'success');

                                // Уменьшаем счетчик непросмотренных
                                updatePendingCount();
                            } else {
                                showNotification(result.message, 'error');
                            }
                        } catch (error) {
                            console.error('Ошибка:', error);
                            showNotification('Ошибка при подтверждении', 'error');
                        }
                    });
                });

                // Игнорирование подмигивания
                document.querySelectorAll('.ignore-wink').forEach(btn => {
                    btn.addEventListener('click', async function () {
                        const winkId = this.dataset.winkId;
                        const row = this.closest('.wink-item');

                        // if (!confirm('Проигнорировать подмигивание?')) return;

                        try {
                            const response = await fetch(`/cabinet/wink/${winkId}/ignore`, {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                    'Accept': 'application/json'
                                }
                            });

                            const result = await response.json();

                            if (result.success) {
                                // Обновляем интерфейс
                                const statusBadge = row.querySelector('.badge');
                                if (statusBadge) {
                                    statusBadge.classList.remove('bg-warning');
                                    statusBadge.classList.add('bg-secondary');
                                    statusBadge.textContent = 'Проигнорировано';
                                }

                                // Удаляем кнопки действий
                                const btnGroup = row.querySelector('.btn-group');
                                if (btnGroup) btnGroup.remove();

                                showNotification(result.message, 'info');

                                // Уменьшаем счетчик непросмотренных
                                updatePendingCount();
                            } else {
                                showNotification(result.message, 'error');
                            }
                        } catch (error) {
                            console.error('Ошибка:', error);
                            showNotification('Ошибка при игнорировании', 'error');
                        }
                    });
                });

                function showNotification(message, type = 'success') {
                    const notification = document.createElement('div');
                    notification.className = `alert alert-${type === 'success' ? 'success' : type === 'error' ? 'danger' : 'info'} alert-dismissible fade show position-fixed`;
                    notification.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
                    notification.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
                    document.body.appendChild(notification);
                    setTimeout(() => notification.remove(), 3000);
                }

                function updatePendingCount() {
                    const pendingBadge = document.querySelector('h1 .badge');
                    if (pendingBadge) {
                        const currentCount = parseInt(pendingBadge.textContent) || 0;
                        if (currentCount > 0) {
                            pendingBadge.textContent = currentCount - 1;
                            if (pendingBadge.textContent === '0') {
                                pendingBadge.remove();
                            }
                        }
                    }
                }
            });
        </script>
    @endpush

    @push('styles')
        <style>
            .wink-item {
                transition: all 0.2s ease;
            }

            .wink-item:hover {
                background-color: #f8f9fa;
                transform: translateX(3px);
            }

            .btn-group .btn {
                transition: all 0.2s ease;
            }

            .btn-group .btn:hover {
                transform: scale(1.05);
            }

            /* Скроллбар */
            .card-body::-webkit-scrollbar {
                width: 5px;
            }

            .card-body::-webkit-scrollbar-track {
                background: #f1f1f1;
                border-radius: 8px;
            }

            .card-body::-webkit-scrollbar-thumb {
                background: #F6A23C;
                border-radius: 8px;
            }

            .card {

                font-family: Montserrat, serif;
                box-shadow: 0 0 30px 0 rgba(0, 0, 0, 0.1);
                background: #fff;
                border-radius: 30px;
            }

            .card-header {
                background: #F6A23C;
                margin-bottom: 5px;
                border-bottom: 0;
                border-radius: 30px 30px 0 0!important;
                padding: 1.5rem 1.5rem;
                color: #fff;
                font-family: Unbounded, serif;
                font-weight: 500;
                font-size: 16px;
            }

            .p-name strong{
                font-size: 16px;
            }

            .p-name strong small{
                font-size: 14px;
            }



        </style>
    @endpush
@endsection
