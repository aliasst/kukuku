@extends('layouts.cabinet')

@section('content')

    <section id="" class="cabinet-section">
        <div class="container">
            <div class="row">
                <div class="col-xs-12">
                    <div class="cabinet-title-section">
                        <H1>Мои подарки</H1>
                    </div>
{{--                    <h1 class="mb-4">--}}
{{--                        🎁 Мои подарки--}}
{{--                        @php--}}
{{--                            $pendingCount = $receivedGifts->where('status', 'pending')->count();--}}
{{--                        @endphp--}}
{{--                        @if($pendingCount > 0)--}}
{{--                            <span class="badge bg-danger">{{ $pendingCount }}</span>--}}
{{--                        @endif--}}
{{--                    </h1>--}}

                    <div class="row">
                        <!-- Полученные подарки -->
                        <div class="col-md-6">
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h5 class="mb-0">
                                        Полученные подарки
                                        <span class="badge bg-danger text-white ms-2">{{ $receivedGifts->count() }}</span>
                                    </h5>
                                </div>
                                <div class="card-body" style="max-height: 600px; overflow-y: auto;">
                                    @if($receivedGifts->count() > 0)
                                        @foreach($receivedGifts as $gift)
                                            <div class="gift-item"
                                                 data-gift-id="{{ $gift->id }}">
                                                <div class="d-flex justify-content-between align-items-start">
                                                    <div class="flex-grow-1">
                                                        <div class="d-flex align-items-center mb-2">
                                                            <div class="gift-icon" style="">
                                                                <div class="gift-circle">
                                                                    <img
                                                                        src="{{ asset($gift->gift->icon)}}"
                                                                        class=""
                                                                        style="">
                                                                </div>

                                                            </div>
                                                            <div class="p-name">
                                                                <strong>{{ $gift->gift->name ?? 'Подарок' }}</strong>

                                                                <br>
                                                                <small class="text-muted">
                                                                    от  {{ $gift->fromUser->name }}
                                                                </small>
                                                            </div>
                                                            @if($gift->status === 'pending')
                                                                <div class="btn-group">
                                                                    <button class="accept-gift"
                                                                            data-gift-id="{{ $gift->id }}"
                                                                            title="Получить подарок">
                                                                        <!--Получить-->
                                                                    </button>
                                                                    <button class="ignore-gift"
                                                                            data-gift-id="{{ $gift->id }}"
                                                                            title="Отказаться">
                                                                        <!--Отказаться-->
                                                                    </button>
                                                                </div>
                                                            @endif
                                                        </div>
                                                        <div class="meta-list">
                                                            <span class="m-calend">
                                                                {{ $gift->event->title }}
                                                            </span>
                                                            <span class="m-date">
                                                                {{ $gift->created_at->format('d.m.Y H:i') }}
                                                            </span>
                                                            <span
                                                                class="badge m-status {{ $gift->status_badge_class }}">
                                                    {{ $gift->status_name }}
                                                            </span>
{{--                                                            @if($gift->message)--}}
{{--                                                                <div--}}
{{--                                                                    class="alert alert-light mt-2 mb-0 py-1 px-2 small">--}}
{{--                                                                    💬 {{ $gift->message }}--}}
{{--                                                                </div>--}}
{{--                                                            @endif--}}
                                                        </div>
                                                    </div>


                                                </div>
                                            </div>
                                        @endforeach
                                    @else
                                        <div class="text-center py-5 text-muted">
                                            🎁 Нет полученных подарков
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Отправленные подарки -->
                        <div class="col-md-6">
                            <div class="card mb-4">
                                <div class="card-header ">
                                    <h5 class="mb-0">
                                        Отправленные подарки
                                        <span class="badge bg-danger ms-2">{{ $sentGifts->count() }}</span>
                                    </h5>
                                </div>
                                <div class="card-body" style="max-height: 600px; overflow-y: auto;">
                                    @if($sentGifts->count() > 0)
                                        @foreach($sentGifts as $gift)
                                            <div class="gift-item-sent">
                                                <div class="d-flex justify-content-between align-items-start">
                                                    <div class="flex-grow-1">
                                                        <div class="d-flex align-items-center mb-2">
                                                            <div class="gift-icon" style="">
                                                                <div class="gift-circle">
                                                                    <img
                                                                        src="{{ asset($gift->gift->icon)}}"
                                                                        class=""
                                                                        style="">
                                                                </div>
                                                            </div>
                                                            <div class="p-name">
                                                                <strong>{{ $gift->gift->name ?? 'Подарок' }}</strong>

                                                                <br>
                                                                <small class="text-muted">
                                                                    для  {{ $gift->toUser->name }}
                                                                </small>
                                                            </div>
                                                        </div>
                                                        <div class="meta-list">
                                                            <span class="m-calend">
                                                                {{ $gift->event->title }}
                                                            </span>
                                                            <span class="m-date">
                                                                {{ $gift->created_at->format('d.m.Y H:i') }}
                                                            </span>
                                                            <span
                                                                class="badge {{ $gift->status_badge_class }}">
                                                    {{ $gift->status_name }}
                                                            </span>
{{--                                                            @if($gift->message)--}}
{{--                                                                <div--}}
{{--                                                                    class="alert alert-light mt-2 mb-0 py-1 px-2 small">--}}
{{--                                                                    💬 {{ $gift->message }}--}}
{{--                                                                </div>--}}
{{--                                                            @endif--}}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    @else
                                        <div class="text-center py-5 text-muted">
                                            📤 Нет отправленных подарков
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
                // Получение подарка (подтверждение)
                document.querySelectorAll('.accept-gift').forEach(btn => {
                    btn.addEventListener('click', async function () {
                        const giftId = this.dataset.giftId;
                        const row = this.closest('.gift-item');

                        // if (!confirm('Получить подарок?')) return;

                        try {
                            const response = await fetch(`/cabinet/gift/${giftId}/accept`, {
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
                                    statusBadge.textContent = 'Получен';
                                }

                                // Удаляем кнопки действий
                                const btnGroup = row.querySelector('.btn-group');
                                if (btnGroup) btnGroup.remove();

                                showNotification(result.message, 'success');

                                // Обновляем счетчик
                                updatePendingCount();
                            } else {
                                showNotification(result.message, 'error');
                            }
                        } catch (error) {
                            console.error('Ошибка:', error);
                            showNotification('Ошибка при получении подарка', 'error');
                        }
                    });
                });

                // Отказ от подарка (игнорирование)
                document.querySelectorAll('.ignore-gift').forEach(btn => {
                    btn.addEventListener('click', async function () {
                        const giftId = this.dataset.giftId;
                        const row = this.closest('.gift-item');

                        // if (!confirm('Отказаться от подарка? Это действие нельзя отменить.')) return;

                        try {
                            const response = await fetch(`/cabinet/gift/${giftId}/ignore`, {
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
                                    statusBadge.textContent = 'Отклонён';
                                }

                                // Удаляем кнопки действий
                                const btnGroup = row.querySelector('.btn-group');
                                if (btnGroup) btnGroup.remove();

                                showNotification(result.message, 'info');

                                // Обновляем счетчик
                                updatePendingCount();
                            } else {
                                showNotification(result.message, 'error');
                            }
                        } catch (error) {
                            console.error('Ошибка:', error);
                            showNotification('Ошибка при отказе от подарка', 'error');
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






        </style>
    @endpush
@endsection
