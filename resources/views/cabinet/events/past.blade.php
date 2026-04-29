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

<!-- Модальное окно для подарков -->
<div class="modal fade" id="giftModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content rounded-4">
            <div class="modal-header border-0" style="">
                <h5 class="modal-title">
                    <span id="giftModalTitle">Подарить подарок</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <!-- Участники (2-3 ряда, имена только в tooltip) -->
                <div class="mb-4">
                    <div class="d-flex justify-content-between align-items-center mb-2" style="display:none!important;">
                        <h6 class="mb-0">👥 Участники события</h6>
                        <span class="badge bg-secondary" id="participantsCount">0</span>
                    </div>
                    <div id="participantsList" class="participants-grid">
                        <div class="text-center p-4 text-muted">🔄 Загрузка...</div>
                    </div>
                </div>

                <!-- Подарки (всплывающая подсказка) -->
                <div>
                    <h6 class="title-choise mb-2"> Выберите подарок</h6>
                    <div class="gifts-grid" id="giftsList">
                        <div class="gift-item" data-gift-id="1" title="Конфета">
                            <div class="gift-circle"><img src="{{ asset('img/gifts/candy.png') }}" alt=""></div>
                            <div class="gift-check">✓</div>
                        </div>
                        <div class="gift-item" data-gift-id="2" title="Букет цветов">
                            <div class="gift-circle"><img src="{{ asset('img/gifts/bouquet.png') }}" alt=""></div>
                            <div class="gift-check">✓</div>
                        </div>
                        <div class="gift-item" data-gift-id="3" title="Шампанское">
                            <div class="gift-circle"><img src="{{ asset('img/gifts/bottle.png') }}" alt=""></div>
                            <div class="gift-check">✓</div>
                        </div>
                        <div class="gift-item" data-gift-id="4" title="Роза">
                            <div class="gift-circle"><img src="{{ asset('img/gifts/rose.png') }}" alt=""></div>
                            <div class="gift-check">✓</div>
                        </div>
                        <div class="gift-item" data-gift-id="5" title="Торт">
                            <div class="gift-circle"><img src="{{ asset('img/gifts/cake.png') }}" alt=""></div>
                            <div class="gift-check">✓</div>
                        </div>
                        <div class="gift-item" data-gift-id="6" title="Фужеры">
                            <div class="gift-circle"><img src="{{ asset('img/gifts/glasses.png') }}" alt=""></div>
                            <div class="gift-check">✓</div>
                        </div>
                        <div class="gift-item" data-gift-id="7" title="Пончик">
                            <div class="gift-circle"><img src="{{ asset('img/gifts/doughnut.png') }}" alt=""></div>
                            <div class="gift-check">✓</div>
                        </div>
                        <div class="gift-item" data-gift-id="8" title="Сюрприз">
                            <div class="gift-circle"><img src="{{ asset('img/gifts/gift.png') }}" alt=""></div>
                            <div class="gift-check">✓</div>
                        </div>
                        <div class="gift-item" data-gift-id="8" title="Мороженое">
                            <div class="gift-circle"><img src="{{ asset('img/gifts/ice-cream.png') }}" alt=""></div>
                            <div class="gift-check">✓</div>
                        </div>
                        <div class="gift-item" data-gift-id="8" title="Напиток">
                            <div class="gift-circle"><img src="{{ asset('img/gifts/drink.png') }}" alt=""></div>
                            <div class="gift-check">✓</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0">
                <div style="display:none;" class="me-auto text-muted" id="selectedUsersCount">✅ Выбрано: 0</div>
                <button style="display:none;" type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                <button type="button" class="orange-btn orange-btn-small" id="sendGiftBtn" disabled>Подарить!</button>
            </div>
        </div>
    </div>
</div>

@push('styles')
    <style>
        #giftModalTitle {
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
            font-size:0;

        }

        .participant-card.selected .participant-check {
            font-size:19px;
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

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let currentEventId = null;
            let participants = [];
            let selectedUsers = [];
            let selectedGiftId = null;

            const giftModal = new bootstrap.Modal(document.getElementById('giftModal'));

            // Открытие модалки
            document.querySelectorAll('.gift-btn').forEach(btn => {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();

                    currentEventId = this.dataset.eventId;
                    const eventTitle = this.dataset.eventTitle;

                    document.getElementById('giftModalTitle').innerHTML = `Подарить подарок: ${eventTitle}`;

                    // Сброс выбора
                    selectedUsers = [];
                    selectedGiftId = null;
                    updateSelectedCount();
                    document.getElementById('sendGiftBtn').disabled = true;

                    // Убираем выделение подарков
                    document.querySelectorAll('.gift-item').forEach(g => g.classList.remove('selected'));

                    // AJAX загрузка участников
                    loadParticipants(currentEventId);

                    giftModal.show();
                });
            });

            function loadParticipants(eventId) {
                const container = document.getElementById('participantsList');
                container.innerHTML = `<div class="text-center p-4 text-muted">🔄 Загрузка участников...</div>`;

                fetch(`/cabinet/events/participants/${eventId}`, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
                })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success && data.participants.length > 0) {
                            participants = data.participants;
                            renderParticipants(participants);
                        } else {
                            container.innerHTML = `<div class="text-center p-4 text-muted">😕 Нет участников</div>`;
                        }
                    })
                    .catch(err => {
                        console.error(err);
                        container.innerHTML = `<div class="text-center p-4 text-danger">⚠️ Ошибка загрузки</div>`;
                    });
            }

            function renderParticipants(list) {
                const container = document.getElementById('participantsList');
                container.innerHTML = '';
                document.getElementById('participantsCount').innerText = list.length;

                list.forEach(p => {
                    const card = document.createElement('div');
                    card.className = 'participant-card';
                    card.dataset.userId = p.id;
                    card.setAttribute('title', p.name); // 👈 Тултип с именем

            //         card.innerHTML = `
            //     <img src="${p.avatar}" class="participant-avatar" alt="${p.name}">
            //     <div class="participant-check">✓</div>
            // `;

                    card.innerHTML = `
                <img src="/storage/img/ava.png" class="participant-avatar" alt="${p.name}">
                <div class="participant-check">✓</div>
            `;

                    card.addEventListener('click', (e) => {
                        e.stopPropagation();
                        const uid = parseInt(card.dataset.userId);
                        const idx = selectedUsers.indexOf(uid);
                        if (idx === -1) {
                            selectedUsers.push(uid);
                            card.classList.add('selected');
                        } else {
                            selectedUsers.splice(idx, 1);
                            card.classList.remove('selected');
                        }
                        updateSelectedCount();
                        checkSendButton();
                    });

                    container.appendChild(card);
                });
            }

            // Выбор подарка
            document.querySelectorAll('.gift-item').forEach(gift => {
                gift.addEventListener('click', () => {
                    const gid = parseInt(gift.dataset.giftId);
                    document.querySelectorAll('.gift-item').forEach(g => g.classList.remove('selected'));
                    gift.classList.add('selected');
                    selectedGiftId = gid;
                    checkSendButton();
                });
            });

            function updateSelectedCount() {
                document.getElementById('selectedUsersCount').innerHTML = `✅ Выбрано: ${selectedUsers.length}`;
            }

            function checkSendButton() {
                document.getElementById('sendGiftBtn').disabled = (selectedUsers.length === 0 || !selectedGiftId);
            }

            // Отправка
            document.getElementById('sendGiftBtn').addEventListener('click', () => {
                if (selectedUsers.length === 0 || !selectedGiftId) return;

                const names = participants.filter(p => selectedUsers.includes(p.id)).map(p => p.name);
                const giftName = document.querySelector(`.gift-item[data-gift-id="${selectedGiftId}"]`)?.getAttribute('title') || 'подарок';

                if (confirm(`Подарок "${giftName}" для:\n${names.join(', ')}\n\nОтправить?`)) {
                    alert(`✅ Подарок отправлен ${names.length} пользователям!`);
                    giftModal.hide();
                }
            });

            // Сброс при закрытии
            document.getElementById('giftModal').addEventListener('hidden.bs.modal', () => {
                selectedUsers = [];
                selectedGiftId = null;
                document.getElementById('sendGiftBtn').disabled = true;
            });
        });
    </script>
@endpush
