@extends('layouts.cabinet')

@section('title', 'Прошедшие мероприятия')

@section('content')

    <section id="" class="cabinet-section cabinet-section-event cabinet-section-event-past">
        <div class="bg-inner"></div>
        <div class="container">
            <div class="row">
                <div class="col-xs-12">
                    <div class="cabinet-title-section">
                        <H1>Прошедшие мероприятия</H1>
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
                                        {{--                                <a href="" type="button" class="reverse-orange-btn orange-btn-part">--}}
                                        {{--                                    Подмигнуть--}}
                                        {{--                                </a>--}}

                                        {{--                                <a href="" type="button" class="reverse-orange-btn orange-btn-part gift-btn"--}}
                                        {{--                                   data-event-id="{{$event->id}}"--}}
                                        {{--                                   data-event-title="{{$event->title}}"--}}
                                        {{--                                >--}}
                                        {{--                                    Подарить подарок--}}
                                        {{--                                </a>--}}

                                        <button type="button" class="reverse-orange-btn orange-btn-part wink-btn"
                                                data-event-id="{{ $event->id }}"
                                                data-event-title="{{ $event->title }}">
                                            Подмигнуть
                                        </button>

                                        <button type="button" class="reverse-orange-btn orange-btn-part gift-btn"
                                                data-event-id="{{ $event->id }}"
                                                data-event-title="{{ $event->title }}">
                                            <i class="fas fa-gift"></i> Подарить подарок
                                        </button>

                                        {{--                                <a href="" type="button" class="reverse-orange-btn orange-btn-part">--}}
                                        {{--                                    Пригласить на встречу--}}
                                        {{--                                </a>--}}

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

{{--<!-- Модальное окно для подарков -->--}}
{{--<div class="modal fade" id="giftModal" tabindex="-1" aria-hidden="true">--}}
{{--    <div class="modal-dialog modal-lg modal-dialog-centered">--}}
{{--        <div class="modal-content rounded-4">--}}
{{--            <div class="modal-header border-0" style="">--}}
{{--                <h5 class="modal-title">--}}
{{--                    <span id="giftModalTitle">Подарить подарок</span>--}}
{{--                </h5>--}}
{{--                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>--}}
{{--            </div>--}}
{{--            <div class="modal-body p-4">--}}
{{--                <!-- Участники (2-3 ряда, имена только в tooltip) -->--}}
{{--                <div class="mb-4">--}}
{{--                    <div class="d-flex justify-content-between align-items-center mb-2" style="display:none!important;">--}}
{{--                        <h6 class="mb-0">👥 Участники события</h6>--}}
{{--                        <span class="badge bg-secondary" id="participantsCount">0</span>--}}
{{--                    </div>--}}
{{--                    <div id="participantsList" class="participants-grid">--}}
{{--                        <div class="text-center p-4 text-muted">🔄 Загрузка...</div>--}}
{{--                    </div>--}}
{{--                </div>--}}

{{--                <!-- Подарки (всплывающая подсказка) -->--}}
{{--                <div>--}}
{{--                    <h6 class="title-choise mb-2"> Выберите подарок</h6>--}}
{{--                    <div class="gifts-grid" id="giftsList">--}}
{{--                        @foreach($gifts as $gift)--}}
{{--                            <div class="gift-item" data-gift-id="{{ $gift->id }}" title="{{ $gift->name }}">--}}
{{--                                <div class="gift-circle"><img src="{{ asset($gift->icon) }}" alt=""></div>--}}
{{--                                <div class="gift-check">✓</div>--}}
{{--                            </div>--}}
{{--                        @endforeach--}}

{{--                    </div>--}}
{{--                </div>--}}
{{--            </div>--}}
{{--            <div class="modal-footer border-0">--}}
{{--                <div style="display:none;" class="me-auto text-muted" id="selectedUsersCount">✅ Выбрано: 0</div>--}}
{{--                <button style="display:none;" type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>--}}
{{--                <button type="button" class="orange-btn orange-btn-small" id="sendGiftBtn" disabled>Подарить!</button>--}}
{{--            </div>--}}
{{--        </div>--}}
{{--    </div>--}}
{{--</div>--}}

<div class="modal fade" id="giftModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content rounded-4">
            <div class="modal-header">
                <h5 class="modal-title"><span id="giftModalTitle">Подарить подарок</span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="mb-4">
                    <div class="d-flex justify-content-between mb-2" style="display:none!important;">
                        <h6>👥 Участники</h6>
                        <span class="badge bg-secondary" id="giftParticipantsCount">0</span>
                    </div>
                    <div id="giftParticipantsList" class="participants-grid">
                        <div class="text-center p-4 text-muted">🔄 Загрузка...</div>
                    </div>
                </div>
                <div>
                    <h6 class="title-choise">Выберите подарок</h6>
                    <div class="gifts-grid">
                        @foreach($gifts as $gift)
                            <div class="gift-item" data-gift-id="{{ $gift->id }}" title="{{ $gift->name }}">
                                <div class="gift-circle"><img src="{{ asset($gift->icon) }}" alt=""></div>
                                <div class="gift-check">✓</div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <div style="display:none;" class="me-auto text-muted" id="selectedGiftUsersCount">✅ Выбрано: 0</div>
                <button style="display:none;" type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена
                </button>
                <button type="button" class="orange-btn orange-btn-small" id="sendGiftBtn" disabled>Подарить</button>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="winkModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content rounded-4">
            <div class="modal-header">
                <h5 class="modal-title"><span id="winkModalTitle">Подмигнуть</span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="mb-4">
                    <div class="d-flex justify-content-between mb-2" style="display:none!important">
                        <h6>👥 Участники события</h6>
                        <span class="badge bg-secondary" id="winkParticipantsCount">0</span>
                    </div>
                    <div id="winkParticipantsList" class="participants-grid">
                        <div class="text-center p-4 text-muted">🔄 Загрузка...</div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <div style="display:none;" class="me-auto text-muted" id="selectedWinkUsersCount">✅ Выбрано: 0</div>
                <button style="display:none;" type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                <button type="button" class="orange-btn orange-btn-small" id="sendWinkBtn" disabled>Подмигнуть</button>
            </div>
        </div>
    </div>
</div>

@push('styles')
    <style>
        .modal-footer {
            border-top: 0 !important;
        }

        .modal-header {
            border-bottom: 0 !important;
        }

        .modal-title {
            font-family: Unbounded, serif;
        }

        .title-choise {
            font-family: Unbounded, serif;
        }

        /* Сетка участников (2-3 ряда) */
        .participants-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            justify-content: flex-start;
            max-height: 320px;
            overflow-y: auto;
            padding: 10px 5px;
            align-content: flex-start;
        }


        .participant-card {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            position: relative;
            transition: transform 0.2s;
            flex: 0 0 auto;
            width: 130px;
        }

        .participant-card:hover {
            transform: scale(1.05);
        }

        /* Аватар */
        .participant-avatar {
            width: 130px;
            height: 130px;
            border-radius: 20px;
            object-fit: cover;
            transition: all 0.2s;
            background: #f8f9fa;
        }

        .participant-card.selected .participant-avatar {
            border-color: #28a745;
            box-shadow: 0 0 0 2px rgba(40, 167, 69, 0.3);
        }

        /* Галочка выбора */
        .participant-check {
            position: absolute;
            bottom: 10px;
            right: 10px;
            border-radius: 6px;
            width: 25px;
            height: 25px;
            background-color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #f6a23c;
            font-weight: bold;
            opacity: 1;
            font-size: 0;

        }

        .participant-card.selected .participant-check {
            font-size: 19px;
        }

        /* Сетка подарков */
        .gifts-grid {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            margin-top: 15px;
        }

        /* Карточка подарка */
        .gift-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            cursor: pointer;
            padding: 7px;
            border-radius: 16px;
            position: relative;
            transition: all 0.2s;
        }

        .gift-item:hover {
            background-color: #fff8e7;
            transform: translateY(-3px);
        }

        .gift-item.selected {
            background-color: #fff3cd;
            border-radius: 20px;
        }

        /* Круглая иконка подарка */
        .gift-circle {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, #fff0e0, #ffe0c0);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 30px;
            border: 2px solid #ffc107;
            transition: all 0.2s;
        }

        .gift-item.selected .gift-circle {
            border-color: #28a745;
            background-color: #d4edda;
            box-shadow: 0 0 0 2px rgba(40, 167, 69, 0.2);
        }

        /* Галочка на подарке */
        .gift-check {
            position: absolute;
            top: 0;
            right: 0;
            width: 22px;
            height: 22px;
            background-color: #28a745;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 12px;
            font-weight: bold;
            opacity: 0;
            border: 2px solid white;
        }

        .gift-item.selected .gift-check {
            /*opacity: 1;*/
        }

        /* Полоса прокрутки */
        .participants-grid::-webkit-scrollbar {
            width: 6px;
        }

        .participants-grid::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }

        .participants-grid::-webkit-scrollbar-thumb {
            background: #ffc107;
            border-radius: 10px;
        }

        /* Для Chrome, Edge, Safari */
        .modal-content * ::-webkit-scrollbar {
            width: 5px;
            background-color: #f6a23c;
        }

        .modal-content * ::-webkit-scrollbar-thumb {
            background-color: #f6a23c;
            border-radius: 5px;
        }

        .modal-content * ::-webkit-scrollbar-thumb:hover {
            background-color: #f6a23c;
        }


    </style>
@endpush

@push('scripts')

    <script src="{{ asset('js/modals.js') }}"></script>
@endpush
