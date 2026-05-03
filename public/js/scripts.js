;(function($) {
    'use strict';


    // Глобальная функция для всплывающих уведомлений
    window.showNotification = function(message, type = 'success') {
        // Удаляем предыдущее уведомление, если оно есть
        const existingToast = document.querySelector('.toast-notification');
        if (existingToast) existingToast.remove();

        const toast = document.createElement('div');
        toast.className = `toast-notification ${type}`;
        toast.innerHTML = `
        <div class="toast-header">
            <strong>${type === 'success' ? '✅ Успешно' : type === 'error' ? '❌ Ошибка' : 'ℹ️ Информация'}</strong>
            <button type="button" class="toast-close">&times;</button>
        </div>
        <div class="toast-body">${message}</div>
    `;

        document.body.appendChild(toast);
        setTimeout(() => toast.classList.add('show'), 10);

        const closeBtn = toast.querySelector('.toast-close');
        closeBtn.addEventListener('click', () => {
            toast.classList.remove('show');
            setTimeout(() => toast.remove(), 300);
        });

        setTimeout(() => {
            toast.classList.remove('show');
            setTimeout(() => toast.remove(), 300);
        }, 4000);
    };




    // ========== 1. ОБЩИЕ НАСТРОЙКИ ==========
    // Маска для телефона и даты (если элементы есть)
    function initMasks() {
        const $birthDate = $('#birth_date');
        if ($birthDate.length) {
            $birthDate.mask('99.99.9999', { placeholder: '__.__.____' });
        }
        const $phone = $('#phone');
        if ($phone.length) {
            $phone.mask('+7 (999) 999-99-99', { placeholder: '_' });
        }
    }

    // ========== 2. ОБРАБОТЧИКИ ЗАГРУЗКИ ФАЙЛОВ ==========
    // Единый обработчик для .fl_inp (одиночный и множественный)
    // $(document).on('change', '.fl_inp, .fl_inp_multi', function(e) {
    //     const $input = $(this);
    //     const $filesMainWrap = $input.closest('.files-main-wrap');
    //     const $fileFormWrap = $input.closest('.file-form-wrap');
    //     const $fileName = $fileFormWrap.find('.file-name');
    //
    //     // Сброс ошибок
    //     $input.closest('.inp-val-wrap').find('.invalid-feedback').removeClass('visible');
    //
    //     // Отображение имени текущего файла
    //     const fileName = $input.val().replace(/.*[\\\/]/, '');
    //     $fileName.html(fileName);
    //
    //     // Если это мульти-поле (.fl_inp_multi) – добавляем ещё один блок
    //     if ($input.hasClass('fl_inp_multi') && !$input.closest('.file-form-wrap').hasClass('cloned')) {
    //         const $clone = $fileFormWrap.clone(true, true);
    //         $clone.addClass('cloned');
    //         $clone.find('.file-name').html('');
    //         $clone.find('.fl_inp, .fl_inp_multi').val('');
    //         $filesMainWrap.append($clone);
    //     }
    //
    //     // Опционально: меняем цвет кнопки
    //     $input.closest('.my-btn').css('backgroundColor', '#008000');
    // });

    // ========== 3. РЕЖИМ РЕДАКТИРОВАНИЯ ПОЛЕЙ (КАРАНДАШ) ==========
    // Переопределяем toggleFieldEdit с учётом сохранения
    const originalToggleFieldEdit = window.toggleFieldEdit;
    window.toggleFieldEdit = function(fieldId, skipSave = false) {
        const $field = $('#' + fieldId);
        if (!$field.length) return;

        const isTextMode = $field.hasClass('text-mode');
        if (isTextMode) {
            // Запоминаем старое значение
            $field.data('old-value', $field.val());
            $field.removeClass('text-mode').addClass('edit-mode');
            if ($field.is('select')) {
                $field.prop('disabled', false);
            } else {
                $field.prop('readonly', false);
            }
            $field.trigger('focus');
            if ($field.is('input')) {
                $field.trigger('select');
            }
        } else {
            // Выход из режима редактирования
            if (!skipSave) {
                const oldValue = $field.data('old-value');
                const newValue = $field.val();
                if (oldValue !== newValue) {
                    let fieldName = fieldId;
                    if (fieldId === 'fullname') fieldName = 'full_name';
                    else if (fieldId === 'birth_date') fieldName = 'birth_date';
                    else if (fieldId === 'gender') fieldName = 'gender';
                    else if (fieldId === 'phone') fieldName = 'phone';
                    else if (fieldId === 'telegram') fieldName = 'telegram';
                    saveField(fieldId, newValue, fieldName);
                }
            }
            $field.removeClass('edit-mode').addClass('text-mode');
            if ($field.is('select')) {
                $field.prop('disabled', true);
            } else {
                $field.prop('readonly', true);
            }
        }
    };

// Для select – сохраняем сразу при изменении
    $(document).on('change', '.field-editable.edit-mode', function(e) {
        if ($(this).is('select')) {
            const $field = $(this);
            const oldValue = $field.data('old-value');
            const newValue = $field.val();
            if (oldValue !== newValue) {
                let fieldId = $field.attr('id');
                let fieldName = fieldId === 'gender' ? 'gender' : fieldId;
                saveField(fieldId, newValue, fieldName);
                $field.data('old-value', newValue);
            }
        }
    });

    // Обработчик клика по иконке-карандашу
    $(document).on('click', '.edit-icon', function(e) {
        e.preventDefault();
        e.stopPropagation();
        const targetId = $(this).data('target');
        if (targetId) {
            toggleFieldEdit(targetId);
        }
    });

    // Enter – сохраняем и закрываем
    $(document).on('keydown', '.field-editable.edit-mode', function(e) {
        const $field = $(this);
        if (e.key === 'Enter' && $field[0].tagName !== 'TEXTAREA') {
            e.preventDefault();
            window.toggleFieldEdit(this.id, false);
        } else if (e.key === 'Escape') {
            // Отмена – возвращаем старое значение и закрываем без сохранения
            const oldValue = $field.data('old-value');
            $field.val(oldValue);
            window.toggleFieldEdit(this.id, true);
        }
    });

    // Клик вне поля – завершение редактирования
    $(document).on('click', function(e) {
        if ($(e.target).closest('.edit-icon').length) return;
        $('.field-editable.edit-mode').each(function() {
            const $field = $(this);
            if (!$field.is(e.target) && !$field.has(e.target).length) {
                window.toggleFieldEdit($field.attr('id'), false);
            }
        });
    });



    function saveField(fieldId, value, fieldName) {
        $.ajax({
            url: '/cabinet/profile/update-field',
            method: 'POST',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
                field: fieldName,
                value: value
            },
            success: function(response) {
                if (response.success) {
                    showNotification('✅ ' + (response.message || 'Поле обновлено'), 'success');
                    // Если нужно обновить отображаемое значение (например, дату)
                    if (fieldName === 'birth_date' && response.display_value) {
                        $('#' + fieldId).val(response.display_value);
                    }
                } else {
                    showNotification('❌ ' + (response.message || 'Ошибка'), 'error');
                    // Восстанавливаем старое значение
                    $('#' + fieldId).val($('#' + fieldId).data('old-value'));
                }
            },
            error: function(xhr) {
                let msg = 'Ошибка сервера';
                if (xhr.responseJSON && xhr.responseJSON.message) msg = xhr.responseJSON.message;
                showNotification('❌ ' + msg, 'error');
                $('#' + fieldId).val($('#' + fieldId).data('old-value'));
            }
        });
    }



    // ========== 4. ПЕРЕД ОТПРАВКОЙ ФОРМЫ ==========
    // Включаем disabled select перед отправкой (иначе его значение не попадёт в POST)
    $('#profileForm').on('submit', function() {
        $('#gender').prop('disabled', false);
    });

    // ========== 5. ДОПОЛНИТЕЛЬНЫЕ УТИЛИТЫ (если нужны) ==========
    // Меню (если есть такой элемент)
    window.menuToggle = function() {
        $('.menu').toggleClass('active');
        $('.menu-accord-ico').toggleClass('open');
    };

    // ========== 6. ИНИЦИАЛИЗАЦИЯ ==========
    $(function() {
        initMasks();
    });

})(jQuery);
