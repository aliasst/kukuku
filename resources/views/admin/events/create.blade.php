@extends('layouts.admin')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>➕ Создание события</h1>
        <a href="{{ route('admin.events.index') }}" class="btn btn-secondary">Назад</a>
    </div>

    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('admin.events.store') }}" enctype="multipart/form-data">
                @csrf

                <div class="row">
                    <div class="col-md-8">
                        <div class="mb-3">
                            <label>Название *</label>
                            <input type="text" name="title" class="form-control @error('title') is-invalid @enderror"
                                   value="{{ old('title') }}" required>
                            @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="mb-3">
                            <label>Дата *</label>
                            <input type="date" name="event_date" class="form-control @error('event_date') is-invalid @enderror"
                                   value="{{ old('event_date') }}" required>
                            @error('event_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label>Краткое описание *</label>
                    <textarea name="description" class="form-control @error('description') is-invalid @enderror"
                              rows="3" required>{{ old('description') }}</textarea>
                    @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="mb-3">
                    <label>Полный текст</label>
                    <textarea name="content" class="form-control @error('content') is-invalid @enderror"
                              rows="6">{{ old('content') }}</textarea>
                    @error('content')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label>Стоимость (₽)</label>
                            <input type="number" name="price" class="form-control" value="{{ old('price', 0) }}">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label>Макс. участников</label>
                            <input type="number" name="max_participants" class="form-control" value="{{ old('max_participants') }}">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label>Изображение</label>
                            <input type="file" name="image" class="form-control" accept="image/*">
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <div class="form-check">
                        <input type="checkbox" name="is_online" class="form-check-input" value="1" {{ old('is_online') ? 'checked' : '' }}>
                        <label class="form-check-label">Онлайн-событие</label>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">Создать</button>
            </form>
        </div>
    </div>
@endsection
