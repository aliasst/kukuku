@extends('layouts.admin')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>📅 Управление событиями</h1>
        <a href="{{ route('admin.events.create') }}" class="btn btn-primary">
            + Создать событие
        </a>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>Название</th>
                        <th>Дата</th>
                        <th>Тип</th>
                        <th>Участников</th>
                        <th>Действия</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($events as $event)
                        <tr>
                            <td>{{ $event->id }}</td>
                            <td>{{ $event->title }}</td>
                            <td>{{ $event->formatted_date }}</td>
                            <td>
                            <span class="badge {{ $event->type_badge_class }}">
                                {{ $event->type_name }}
                            </span>
                            </td>
                            <td>{{ $event->users_count }}</td>
                            <td>
                                <a href="{{ route('admin.events.edit', $event) }}" class="btn btn-sm btn-warning">
                                    ✏️
                                </a>
                                <form action="{{ route('admin.events.destroy', $event) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Удалить событие?')">
                                        🗑️
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            {{ $events->links() }}
        </div>
    </div>
@endsection
