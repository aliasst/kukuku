@extends('layouts.cabinet')

@section('title', 'Профиль')

@section('content')



    <section id="" class="">
        <div class="container">
            <div class="row">
                <div class="col-xs-12">

                    <div class="participant-card">
                        <div class="card-inner">
                            <!-- Верхний блок с KuKu'Ku и декоративным чипом -->

                            <form method="POST" action="{{ route('profile.store') }}" id="profileForm" enctype="multipart/form-data">
                                @if (session('status'))
                                    <div class="alert alert-success" role="alert">
                                        {{ session('status') }}
                                    </div>
                                @endif
                                @if (session('success'))
                                    <div class="alert alert-success">
                                        {{ session('success') }}
                                    </div>
                                @endif
                                @if ($errors->any())
                                    <div class="alert alert-danger">
                                        <ul>
                                            @foreach ($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>

                                @endif
                                @csrf
                            <!-- Основная сетка: Левые данные / Правые действия загрузки (Bootstrap row + flex) -->
                            <div class="row g-4">
                                <!-- ЛЕВАЯ КОЛОНКА: персональная информация -->
                                <div class="col-lg-4 col-md-12">
                                    <div class="info-section">


                                        <!-- ФИО -->
                                        <div class="info-item">
                                            <div class="info-item-ico">
                                                <img src="{{ asset('img/icons/fio.svg') }}" alt="">
                                            </div>
                                            <div class="info-item-main">
                                                <div class="info-label">ФИО</div>

                                                <div class="info-value">
                                                    <!-- Поле 1: Имя (input) -->
                                                    <div class="form-group">
                                                        <div class="editable-field-wrapper">
                                                            <div class="flex-grow-1">
                                                                <input type="text" class="field-editable text-mode"
                                                                       id="fullname"
                                                                       name="full_name"
                                                                       value="{{ old('full_name', $profile->full_name ?? '') }}">
                                                            </div>
                                                            <button type="button" class="edit-icon" data-target="fullname"/>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>


                                        </div>
                                        <!-- Дата рождения -->
                                        <div class="info-item">
                                            <div class="info-item-ico">
                                                <img src="{{ asset('img/icons/bd.svg') }}" alt="">
                                            </div>
                                            <div class="info-item-main">
                                                <div class="info-label">Дата рождения</div>

                                                <div class="info-value">
                                                    <!-- Поле 2: Дата рождения (input) -->
                                                    <div class="form-group">
                                                        <div class="editable-field-wrapper">
                                                            <div class="flex-grow-1">
                                                                <input type="text" class="field-editable text-mode"
                                                                       id="birth_date"
                                                                       name="birth_date"
                                                                       value="{{ old('birth_date', $profile->getFormattedDateAttribute()) }}">
                                                            </div>
                                                            <button type="button" class="edit-icon" data-target="birth_date"/>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- Пол -->
                                        <div class="info-item">
                                            <div class="info-item-ico">
                                                <img src="{{ asset('img/icons/gender.svg') }}" alt="">
                                            </div>
                                            <div class="info-item-main">
                                                <div class="info-label">Пол</div>
                                                <div class="info-value">
                                                    <!-- Поле 3: Пол (select) -->
                                                    <div class="form-group">
                                                        <div class="editable-field-wrapper">
                                                            <div class="flex-grow-1">
                                                                <select class="form-control field-editable text-mode"
                                                                        id="gender"
                                                                        name="gender"
                                                                        disabled>
                                                                    <option value="male" @if ($profile->gender == 'male') selected @endif>Мужской</option>
                                                                    <option value="female" @if ($profile->gender == 'female')selected @endif>Женский</option>
                                                                </select>
                                                            </div>
                                                            <button type="button" class="edit-icon" data-target="gender">

                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- Телефон -->
                                        <div class="info-item">
                                            <div class="info-item-ico">
                                                <img src="{{ asset('img/icons/phone-ico.svg') }}" alt="">
                                            </div>
                                            <div class="info-item-main">
                                                <div class="info-label">Телефон</div>
                                                <div class="info-value">
                                                    <div class="form-group">
                                                        <div class="editable-field-wrapper">
                                                            <div class="flex-grow-1">
                                                                <input type="text" class="field-editable text-mode"
                                                                       id="phone"
                                                                       name="phone"
                                                                       value="{{ old('phone', $profile->phone ?? '') }}">
                                                            </div>
                                                            <button type="button" class="edit-icon" data-target="phone"/>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>


                                        <!-- Telegram -->
                                        <div class="info-item">
                                            <div class="info-item-ico">
                                                <img src="{{ asset('img/icons/teleg-ico.svg') }}" alt="">
                                            </div>
                                            <div class="info-item-main">
                                                <div class="info-label">Telegram</div>
                                                <div class="info-value">
                                                    <div class="editable-field-wrapper">
                                                        <div class="flex-grow-1">
                                                            <input type="text" class="field-editable text-mode"
                                                                   id="telegram"
                                                                   name="telegram"
                                                                   value="{{ old('telegram', $profile->telegram ?? '') }}">
                                                        </div>
                                                        <button type="button" class="edit-icon" data-target="telegram"/>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>




                                    </div>
                                </div>

                                <!-- ПРАВАЯ КОЛОНКА: Загрузка фото, документов, файл-ссылка -->
                                <div class="col-lg-8 col-md-12">
                                    <div class="cabinet-actions-section">

                                        <div class="cabinet-actions-section-row">
                                            <div class="cabinet-actions-section-item">
                                                <div class="image-preview">
                                                    <img
                                                        @if(Auth::user()->profile->avatar)
                                                            src="{{ Storage::url(Auth::user()->profile->avatar) }}"
                                                        @else
                                                            src="{{ asset('img/prev-1.png') }}"
                                                        @endif
                                                    >
                                                </div>
                                                <div class="item-actons">
                                                    <span class="item-actions-name">Фото</span>
                                                    <div class="files-main-wrap">
                                                    <div class="file-form-wrap">

                                                        <div class="file-upload my-btn">
                                                            <label>
                                                                <input class="fl_inp " type="file" name="avatar">
                                                                <span>загрузить</span>
                                                            </label>
                                                        </div>
                                                        <div class="file-name"></div>
                                                    </div>
                                                </div>
                                                </div>

                                            </div>

                                            <div class="cabinet-actions-section-item">
                                                <div class="image-preview">
                                                    <img src="{{ asset('img/prev-2.png') }}" alt="">
                                                </div>
                                                <div class="item-actons">
                                                    <span class="item-actions-name">Документы</span>
                                                    <div class="files-main-wrap">
                                                        <div class="file-form-wrap">

                                                            <div class="file-upload my-btn">
                                                                <label>
                                                                    <input class="fl_inp" type="file" name="documents[]" multiple="">
                                                                    <span>загрузить</span>
                                                                </label>
                                                            </div>
                                                            <div class="file-name"></div>
                                                        </div>
                                                    </div>
                                                </div>

                                            </div>


                                            <div class="cabinet-actions-section-item">
                                                <div class="image-preview">
                                                    <img src="{{ asset('img/prev-3.png') }}" alt="">
                                                </div>
                                                <div class="item-actons">
                                                    <span class="item-actions-name">Файл</span>
                                                    @if(!$files->isEmpty())
                                                        <div class="uploaded-files">
                                                            <div class="uploaded-files-label">Ранее загруженный файлы:</div>
                                                            <div class="uploaded-files-inner-wrap">
                                                                @foreach($files as $file)
                                                                    <div class="uploaded-file" id="{{$file->id}}">
                                                                        <a style="text-decoration: underline" target="_blank"
                                                                           href="{{ $file->getDownloadUrlAttribute() }}">{{$file->file_name}}</a>
                                                                            <a class="btn-link js-delete-file" href="#"
                                                                               data-id="{{$file->id}}">Удалить</a>
                                                                    </div>

                                                                @endforeach
                                                            </div>

                                                        </div>
                                                    @endif
                                                    <div class="files-main-wrap">
                                                        <div class="file-form-wrap">

                                                            <div class="file-upload my-btn">
                                                                <label>
                                                                    <input class="fl_inp" type="file" name="files[]" multiple="">
                                                                    <span>Загрузить</span>
                                                                </label>
                                                            </div>
                                                            <div class="file-name"></div>
                                                        </div>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>





                                    </div>

                                    <div class="btn-section">

                                        <div class="save-btn-wrapper">

                                            <button type="submit" class="orange-btn orange-btn-small">
                                                Сохранить
                                            </button>
                                        </div>
                                    </div>




                                </div>
                            </div>
                            </form>

                            <!-- Кнопка СОХРАНИТЬ внизу карточки — по центру справа (футер) -->

                        </div>
                    </div>


                </div>
            </div>
        </div>
    </section>


@endsection
