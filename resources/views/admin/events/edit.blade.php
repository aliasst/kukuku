@extends('layouts.admin')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>✏️ Редактирование: {{ $event->title }}</h1>
        <a href="{{ route('admin.events.index') }}" class="btn btn-secondary">Назад</a>
    </div>

    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('admin.events.update', $event) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="row">
                    <div class="col-md-8">
                        <div class="mb-3">
                            <label>Название *</label>
                            <input type="text" name="title" class="form-control @error('title') is-invalid @enderror"
                                   value="{{ old('title', $event->title) }}" required>
                            @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="mb-3">
                            <label>Дата *</label>
                            <input type="date" name="event_date" class="form-control @error('event_date') is-invalid @enderror"
                                   value="{{ old('event_date', $event->event_date->format('Y-m-d')) }}" required>
                            @error('event_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label>Краткое описание *</label>
                    <textarea name="description" class="form-control @error('description') is-invalid @enderror"
                              rows="3" required>{{ old('description', $event->description) }}</textarea>
                    @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="mb-3">
                    <label>Полный текст</label>
                    <textarea name="content" class="form-control @error('content') is-invalid @enderror"
                              rows="6">{{ old('content', $event->content) }}</textarea>
                    @error('content')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label>Стоимость (₽)</label>
                            <input type="number" name="price" class="form-control" value="{{ old('price', $event->price) }}">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label>Макс. участников</label>
                            <input type="number" name="max_participants" class="form-control" value="{{ old('max_participants', $event->max_participants) }}">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Изображение события</label>

                            <!-- КРАСНОЕ ПРЕДУПРЕЖДЕНИЕ О РАЗМЕРЕ -->
                            <div class="alert alert-danger mb-3 py-2" style="font-size: 14px; background-color: #fff5f5; border-left: 4px solid #dc3545;">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                <strong>Важно!</strong> Для корректного отображения используйте изображение размером <strong>313 × 231 пикселей</strong>
                            </div>

                            <!-- Текущее изображение -->
                            @if(isset($event) && $event->image)
                                <div class="mb-3">
                                    <label class="form-label">Текущее изображение:</label>
                                    <div class="border rounded p-2 d-inline-block bg-light">
                                        <img src="{{ Storage::url($event->image) }}" class="img-fluid rounded" style="max-height: 100px;">
                                    </div>
                                </div>
                            @endif

                            <div class="alert alert-secondary mb-3 py-2" style="font-size: 13px; background-color: #f8f9fa;">
                                <i class="fas fa-info-circle me-1"></i>
                                Поддерживаемые форматы: JPG, JPEG, PNG, WEBP. Максимальный размер: 5MB
                            </div>

                            <input type="file"
                                   name="image"
                                   class="form-control @error('image') is-invalid @enderror"
                                   accept="image/jpeg,image/png,image/jpg,image/webp"
                                   onchange="previewImage(this)">

                            <!-- Превью нового изображения -->
                            <div id="imagePreview" class="mt-3" style="display: none;">
                                <div class="border rounded p-3 bg-light">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <img id="preview" src="#" alt="Preview" class="img-fluid rounded">
                                        </div>
                                        <div class="col-md-8">
                                            <div id="sizeWarning" class="mt-2"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            @error('image')
                            <div class="invalid-feedback d-block mt-1">{{ $message }}</div>
                            @enderror

                            <div class="mt-3">
                                <div class="alert alert-warning py-2" style="font-size: 12px; background-color: #fff3cd;">
                                    <i class="fas fa-info-circle me-1"></i>
                                    <strong>Обратите внимание:</strong> Изображение сохраняется в оригинальном размере.
                                    Пожалуйста, подготовьте картинку размером <strong>313×231px</strong> перед загрузкой.
                                </div>
                            </div>
                        </div>

                        <script>
                            function previewImage(input) {
                                const previewDiv = document.getElementById('imagePreview');
                                const preview = document.getElementById('preview');
                                const sizeWarning = document.getElementById('sizeWarning');

                                if (input.files && input.files[0]) {
                                    const file = input.files[0];
                                    const reader = new FileReader();

                                    reader.onload = function(e) {
                                        preview.src = e.target.result;
                                        previewDiv.style.display = 'block';

                                        const img = new Image();
                                        img.onload = function() {
                                            const width = this.width;
                                            const height = this.height;

                                            if (width === 313 && height === 231) {
                                                sizeWarning.innerHTML = `
                        <div class="alert alert-success py-1 mb-0" style="font-size: 13px;">
                            <i class="fas fa-check-circle me-1"></i>
                            ✓ Размер: ${width}×${height}px — идеально!
                        </div>
                    `;
                                            } else {
                                                sizeWarning.innerHTML = `
                        <div class="alert alert-danger py-2 mb-0" style="font-size: 13px;">
                            <i class="fas fa-exclamation-triangle me-1"></i>
                            <strong>⚠️ Неподходящий размер изображения!</strong><br>
                            Текущий: ${width}×${height}px<br>
                            Требуется: <strong>313×231px</strong><br>
                            <small class="text-muted">Изображение будет сохранено как есть, но может отображаться некорректно.</small>
                        </div>
                    `;
                                            }
                                        };
                                        img.src = e.target.result;
                                    };

                                    reader.readAsDataURL(file);
                                }
                            }
                        </script>
                    </div>
                </div>

                <div class="mb-3">
                    <div class="form-check">
                        <input type="checkbox" name="is_online" class="form-check-input" value="1" {{ old('is_online', $event->is_online) ? 'checked' : '' }}>
                        <label class="form-check-label">Онлайн-событие</label>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">Сохранить</button>
                <a href="{{ route('admin.events.index') }}" class="btn btn-secondary">Отмена</a>
            </form>
        </div>
    </div>
@endsection
