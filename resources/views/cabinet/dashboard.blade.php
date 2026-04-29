@extends('layouts.cabinet')

@section('title', 'Личный кабинет')

@section('content')

    <section id="" class="cabinet-section cabinet-section-dashboard cabinet-actions-section-h-center">
        <div class="container">
            <div class="row">
                <div class="col-xs-12">
                    <div class="cabinet-title-section">
                        <H1 class="text-center">Выберите действие</H1>
                    </div>


                    <div class="actions-row">
                        <div class="actions-item">
                            <div class="actions-card">
                                <div class="actions-prew"><img src="{{ asset('img/action-1.png') }}" alt=""></div>

                                <div class="actions-title">Карточка участника</div>
                            </div>
                            <div class="event-actions">
                                <a href="" type="button" class="orange-btn orange-btn-small">
                                    Редактировать
                                </a>



                            </div>


                        </div>


                        <div class="actions-item">
                            <div class="actions-card">
                                <div class="actions-prew"><img src="{{ asset('img/action-2.png') }}" alt=""></div>

                                <div class="actions-title">Будущие мероприятия</div>
                            </div>
                            <div class="event-actions">
                                <a href="{{ route('events.upcoming') }}" type="button" class="orange-btn orange-btn-small">
                                    Смотреть
                                </a>



                            </div>


                        </div>



                        <div class="actions-item">
                            <div class="actions-card">
                                <div class="actions-prew"><img src="{{ asset('img/action-3.png') }}" alt=""></div>

                                <div class="actions-title">Прошедшие мероприятия</div>
                            </div>
                            <div class="event-actions">
                                <a href="{{ route('events.past') }}" type="button" class="orange-btn orange-btn-small">
                                    Смотреть
                                </a>



                            </div>


                        </div>









                    </div>



                </div>
            </div>
        </div>
    </section>


@endsection
