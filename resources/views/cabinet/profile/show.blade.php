@extends('layouts.cabinet')

@section('title', 'Профиль')

@section('content')

    <section id="" class="">
        <div class="container">
            <div class="row">
                <div class="col-xs-12">
                    <div class="cabinet-title-section">
                        <h1>КАРТОЧКА УЧАСТНИКА</h1>
                    </div>

                    <div class="participant-card">
                        <div class="card-inner">
                            <!-- Верхний блок с KuKu'Ku и декоративным чипом -->

                            <form method="POST" action="{{ route('profile.store') }}" id="profileForm"
                                  enctype="multipart/form-data">
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
                                                                <button type="button" class="edit-icon"
                                                                        data-target="fullname"/>
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
                                                                <button type="button" class="edit-icon"
                                                                        data-target="birth_date"/>
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
                                                                    <select
                                                                        class="form-control field-editable text-mode"
                                                                        id="gender"
                                                                        name="gender"
                                                                        disabled>
                                                                        <option value="male"
                                                                                @if ($profile->gender == 'male') selected @endif>
                                                                            Мужской
                                                                        </option>
                                                                        <option value="female"
                                                                                @if ($profile->gender == 'female')selected @endif>
                                                                            Женский
                                                                        </option>
                                                                    </select>
                                                                </div>
                                                                <button type="button" class="edit-icon"
                                                                        data-target="gender">

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
                                                                <button type="button" class="edit-icon"
                                                                        data-target="phone"/>
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
                                                            <button type="button" class="edit-icon"
                                                                    data-target="telegram"/>
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
                                                <div class="cabinet-actions-section-item" id="avatar-upload-section">
                                                    <div class="image-preview">
                                                        <img id="avatar-preview"
                                                             src="{{ Storage::url(Auth::user()->profile->avatar ?? '') }}"
                                                             onerror="this.src='{{ asset('img/prev-1.png') }}'">
                                                    </div>
                                                    <div class="item-actons">
                                                        <span class="item-actions-name">Фото</span>
                                                        <div class="files-main-wrap">
                                                            <div class="file-form-wrap">
                                                                <div class="file-upload my-btn">
                                                                    <label>
                                                                        <input type="file" id="avatar-input"
                                                                               name="avatar"
                                                                               accept="image/jpeg,image/png,image/jpg,image/webp">
                                                                        <span>загрузить</span>
                                                                    </label>
                                                                </div>
                                                                <div id="avatar-message" class="small mt-1"></div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="cabinet-actions-section-item" id="documents-upload-section">
                                                    <div class="image-preview">
                                                        <img src="{{ asset('img/prev-2.png') }}" alt="">
                                                    </div>
                                                    <div class="item-actons">
                                                        <span class="item-actions-name">Документы</span>
                                                        <div class="files-main-wrap">
                                                            <div class="file-form-wrap">
                                                                <div class="file-upload my-btn">
                                                                    <label>
                                                                        <input type="file" id="documents-input"
                                                                               class="fl_inp_multi" name="documents[]"
                                                                               multiple
                                                                               accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.zip,.rar,.txt">
                                                                        <span>загрузить</span>
                                                                    </label>
                                                                </div>
                                                                <div class="file-name"></div>
                                                                <div id="documents-message" class="small mt-1"></div>
                                                            </div>
                                                        </div>
                                                        <div class="file-list" id="documents-list">
                                                            @foreach($documents as $doc)
                                                                <div class="uploaded-file" id="doc-{{ $doc->id }}">
                                                                    <a style="text-decoration: underline"
                                                                       target="_blank"
                                                                       href="{{ $doc->getDownloadUrlAttribute() }}">{{ $doc->file_name }}</a>
                                                                    <a class="btn-link js-delete-file" href="#"
                                                                       data-id="{{ $doc->id }}" data-type="document">Удалить</a>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                </div>


                                                <div class="cabinet-actions-section-item" id="files-upload-section">
                                                    <div class="image-preview">
                                                        <img src="{{ asset('img/prev-3.png') }}" alt="">
                                                    </div>
                                                    <div class="item-actons">
                                                        <span class="item-actions-name">Файлы</span>
                                                        <div class="files-main-wrap">
                                                            <div class="file-form-wrap">
                                                                <div class="file-upload my-btn">
                                                                    <label>
                                                                        <input type="file" id="files-input"
                                                                               class="fl_inp_multi" name="files[]"
                                                                               multiple
                                                                               accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.zip,.rar,.txt">
                                                                        <span>загрузить</span>
                                                                    </label>
                                                                </div>
                                                                <div class="file-name"></div>
                                                                <div id="documents-message" class="small mt-1"></div>
                                                            </div>
                                                        </div>
                                                        <div class="file-list" id="files-list">
                                                            @foreach($files as $file)
                                                                <div class="uploaded-file" id="doc-{{ $file->id }}">
                                                                    <a style="text-decoration: underline"
                                                                       target="_blank"
                                                                       href="{{ $file->getDownloadUrlAttribute() }}">{{ $file->file_name }}</a>
                                                                    <a class="btn-link js-delete-file" href="#"
                                                                       data-id="{{ $file->id }}" data-type="file">Удалить</a>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                </div>



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

@push('scripts')
    <script>
        $(document).ready(function () {
            $('#avatar-input').on('change', function () {
                var file = this.files[0];
                if (!file) return;

                // Валидация типа и размера
                var allowed = ['image/jpeg', 'image/png', 'image/jpg', 'image/webp'];
                if (!allowed.includes(file.type)) {
                    showNotification('❌ Только JPG, PNG, WEBP', 'error');
                    $(this).val('');
                    return;
                }
                if (file.size > 2 * 1024 * 1024) {
                    showNotification('❌ Файл не более 2MB', 'error');
                    $(this).val('');
                    return;
                }

                // Показываем уведомление о загрузке
                showNotification('⏳ Загрузка аватара...', 'info');

                var formData = new FormData();
                formData.append('avatar', file);
                formData.append('_token', $('meta[name="csrf-token"]').attr('content'));

                $.ajax({
                    url: '{{ route("profile.upload-avatar") }}',
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function (response) {
                        if (response.success) {
                            $('#avatar-preview').attr('src', response.avatar_url + '?t=' + new Date().getTime());
                            showNotification('✅ Аватар обновлён', 'success');
                        } else {
                            showNotification('❌ ' + (response.message || 'Ошибка'), 'error');
                        }
                        $('#avatar-input').val('');
                    },
                    error: function (xhr) {
                        var msg = 'Ошибка загрузки';
                        if (xhr.responseJSON && xhr.responseJSON.message) msg = xhr.responseJSON.message;
                        showNotification('❌ ' + msg, 'error');
                        $('#avatar-input').val('');
                    }
                });
            });
        });

    </script>
    <script>
        $(document).ready(function() {
            $('#documents-input').on('change', function() {
                var files = this.files;
                if (!files.length) return;

                var formData = new FormData();
                for (var i = 0; i < files.length; i++) {
                    formData.append('documents[]', files[i]);
                }
                formData.append('_token', $('meta[name="csrf-token"]').attr('content'));

                // Показываем уведомление о начале загрузки
                showNotification('⏳ Загрузка ' + files.length + ' документов...', 'info');

                $.ajax({
                    url: '{{ route("profile.upload-documents") }}',
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        if (response.success) {
                            showNotification('✅ Загружено ' + response.files.length + ' документов', 'success');

                            // Добавляем каждый файл в список
                            $.each(response.files, function(idx, file) {
                                var newDocHtml = `
                            <div class="uploaded-file" id="doc-${file.id}">
                                <a style="text-decoration: underline" target="_blank" href="${file.url}">${file.name}</a>
                                <a class="btn-link js-delete-file" href="#" data-id="${file.id}" data-type="document">Удалить</a>
                            </div>
                        `;
                                $('#documents-list').append(newDocHtml);
                            });

                            // Очищаем input, чтобы можно было загрузить те же файлы снова
                            $('#documents-input').val('');
                        } else {
                            showNotification('❌ Ошибка загрузки: ' + (response.message || ''), 'error');
                            $('#documents-input').val('');
                        }
                    },
                    error: function(xhr) {
                        var msg = 'Ошибка сервера';
                        if (xhr.responseJSON && xhr.responseJSON.message) msg = xhr.responseJSON.message;
                        showNotification('❌ ' + msg, 'error');
                        $('#documents-input').val('');
                    }
                });
            });
        });
    </script>
    <script>
        $(document).ready(function() {
            $('#files-input').on('change', function() {
                var files = this.files;
                if (!files.length) return;

                var formData = new FormData();
                for (var i = 0; i < files.length; i++) {
                    formData.append('files[]', files[i]);
                }
                formData.append('_token', $('meta[name="csrf-token"]').attr('content'));

                showNotification('⏳ Загрузка ' + files.length + ' файлов...', 'info');

                $.ajax({
                    url: '{{ route("profile.upload-files") }}',   // создадим маршрут
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        if (response.success) {
                            showNotification('✅ Загружено ' + response.files.length + ' файлов', 'success');

                            // Добавляем каждый файл в список
                            $.each(response.files, function(idx, file) {
                                var newFileHtml = `
                            <div class="uploaded-file" id="doc-${file.id}">
                                <a style="text-decoration: underline" target="_blank" href="${file.url}">${file.name}</a>
                                <a class="btn-link js-delete-file" href="#" data-id="${file.id}" data-type="file">Удалить</a>
                            </div>
                        `;
                                $('#files-list').append(newFileHtml);  // используем тот же контейнер, что и для документов
                            });

                            // Очищаем поле ввода, чтобы можно было загрузить другие файлы
                            $('#files-input').val('');
                        } else {
                            showNotification('❌ Ошибка загрузки: ' + (response.message || ''), 'error');
                            $('#files-input').val('');
                        }
                    },
                    error: function(xhr) {
                        var msg = 'Ошибка сервера';
                        if (xhr.responseJSON && xhr.responseJSON.message) msg = xhr.responseJSON.message;
                        showNotification('❌ ' + msg, 'error');
                        $('#files-input').val('');
                    }
                });
            });
        });
    </script>
    <script>
        $(document).on('click', '.js-delete-file', function(e) {
            e.preventDefault();

            const $btn = $(this);
            const $fileBlock = $btn.closest('.uploaded-file'); // ← главное изменение
            const id = $btn.data('id');
            const type = $btn.data('type'); // 'document' или 'file'

            if (!confirm('Удалить файл?')) return;

            $.ajax({
                url: `/cabinet/profile/delete-file/${id}`,
                method: 'DELETE',
                data: { type: type },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        showNotification(response.message, 'success');
                        $fileBlock.fadeOut(300, function() {
                            $(this).remove();
                        });
                    } else {
                        showNotification(response.message || 'Ошибка удаления', 'error');
                    }
                },
                error: function(xhr) {
                    let msg = 'Ошибка сервера';
                    if (xhr.responseJSON && xhr.responseJSON.message) msg = xhr.responseJSON.message;
                    showNotification(msg, 'error');
                }
            });
        });
    </script>

@endpush

