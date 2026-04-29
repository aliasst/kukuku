@extends('layouts.cabinet')

@section('title', 'Будущие Мероприятия')

@section('content')

    <section id="" class="cabinet-section cabinet-section-event cabinet-section-event-future">
        <div class="bg-inner"></div>
        <div class="container">
            <div class="row">
                <div class="col-xs-12">
                    <div class="cabinet-title-section">
                        <H1>Будущие мероприятия</H1>
                    </div>


                    @if($events->count() > 0)
                    <div class="events-row">
                        @foreach($events as $event)
                        <div class="evant-item">
                            <div class="event-card">
                                <div class="evint-prew"><img src="{{ $event->getImageUrl() }}" alt=""></div>
                                <div class="event-content">
                                    <div class="event-title">{{ $event->title }}</div>
                                    <div class="event-meta">
                                        <div class="event-meta-row">
                                            <div class="event-meta-item event-date">
                                                <span>Дата события:</span>
                                                <span>{{ $event->formatted_date }}</span>
                                            </div>
                                            <div class="event-meta-item event-point">
                                                <span>Адрес:</span>
                                                <span>{{ $event->location }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="event-price">
                                        <span>Стоимость</span>
                                        <span>{{ $event->formatted_price }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="event-actions">
{{--                                <a href="" type="button" class="orange-btn orange-btn-by">--}}
{{--                                    Купить билет!--}}
{{--                                </a>--}}
                                @php
                                    $userStatus = $event->getUserStatus(Auth::id());
                                @endphp

                                @if($userStatus === 'registered')
                                    <button class="reverse-orange-btn orange-btn-part" disabled>
                                        Вы участник!
                                    </button>
                                @elseif($userStatus === 'confirmed')
                                    <button class="btn btn-success w-100" disabled>
                                        <i class="fas fa-check-double"></i> Участие подтверждено
                                    </button>
                                @elseif($userStatus === 'attended')
                                    <button class="btn btn-secondary w-100" disabled>
                                        <i class="fas fa-star"></i> Вы участвовали
                                    </button>
                                @elseif($userStatus === 'cancelled')
                                    <button class="btn btn-secondary w-100" disabled>
                                        <i class="fas fa-ban"></i> Запись отменена
                                    </button>
                                @else
                                    <button class="reverse-orange-btn orange-btn-part register-btn"
                                            data-event-id="{{ $event->id }}"
                                            data-event-title="{{ $event->title }}">
                                            Буду учавствовать
                                    </button>
                                @endif
                            </div>


                        </div>
                        @endforeach


                    </div>
                    @endif


                </div>
            </div>
        </div>
    </section>

@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            // Обработчик AJAX-регистрации
            $('.register-btn').on('click', function() {
                const $btn = $(this);
                const eventId = $btn.data('event-id');
                const eventTitle = $btn.data('event-title');
                const $card = $btn.closest('.col-md-4');

                // Блокируем кнопку и показываем загрузку
                $btn.prop('disabled', true);
                const originalText = $btn.html();
                $btn.html('Регистрация...');

                // Отправляем AJAX запрос
                $.ajax({
                    url: '{{ route("events.ajax.register") }}',
                    method: 'POST',
                    data: {
                        event_id: eventId,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success) {
                            // Обновляем текст кнопки
                            $btn.html('Вы участник');
                            $btn.removeClass('btn-primary').addClass('reg-success');
                            $btn.prop('disabled', true);

                            // Обновляем счетчик свободных мест
                            if (response.stats.places_left !== null) {
                                $card.find('.places-left').text(response.stats.places_left);
                            }

                            // Показываем уведомление
                            showNotification('success', 'Успешно!', response.message);
                        }
                    },
                    error: function(xhr) {
                        let errorMessage = 'Ошибка регистрации';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }

                        // Возвращаем кнопку в исходное состояние
                        $btn.prop('disabled', false);
                        $btn.html(originalText);

                        showNotification('error', 'Ошибка', errorMessage);
                    }
                });
            });

            // Функция для показа уведомлений
            function showNotification(type, title, message) {
                const notification = $(`
            <div class="toast-notification ${type}">
                <div class="toast-header">
                    <strong>${title}</strong>
                    <button type="button" class="toast-close">&times;</button>
                </div>
                <div class="toast-body">
                    ${message}
                </div>
            </div>
        `);

                $('body').append(notification);

                // Анимация появления
                setTimeout(() => notification.addClass('show'), 10);

                // Закрытие через 3 секунды
                setTimeout(() => {
                    notification.removeClass('show');
                    setTimeout(() => notification.remove(), 300);
                }, 3000);

                // Закрытие по кнопке
                notification.find('.toast-close').on('click', function() {
                    notification.removeClass('show');
                    setTimeout(() => notification.remove(), 300);
                });
            }
        });
    </script>

    <style>
        .toast-notification {
            position: fixed;
            top: 20px;
            right: 20px;
            min-width: 300px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.15);
            z-index: 10000;
            transform: translateX(400px);
            transition: transform 0.3s ease;
        }

        .toast-notification.show {
            transform: translateX(0);
        }

        .toast-notification.success {
            border-left: 4px solid #f6a23c;
        }

        .toast-notification.error {
            border-left: 4px solid #dc3545;
        }

        .toast-header {
            padding: 12px 15px;
            background: #f8f9fa;
            border-radius: 8px 8px 0 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-family: Unbounded, serif;
        }

        .toast-body {
            padding: 12px 15px;
            font-family: Montserrat, serif;
            font-weight: 500;
        }

        .toast-close {
            background: none;
            border: none;
            font-size: 20px;
            cursor: pointer;
            color: #999;
        }

        .toast-close:hover {
            color: #333;
        }
    </style>
@endpush
