@extends('layouts.admin')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>👥 Управление пользователями</h1>
    </div>

    <div class="card">
        <div class="card-body">
            <table class="table table-hover">
                <thead>
                <tr><th>ID</th><th>Имя</th><th>Email</th><th>Роль</th><th>Дата</th><th>Действия</th></tr>
                </thead>
                <tbody>
                @foreach($users as $user)
                    <tr>
                        <td>{{ $user->id }}</td>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>
                            <span class="badge {{ $user->role_badge_class }}">{{ $user->role_name }}</span>
                        </td>
                        <td>{{ $user->created_at->format('d.m.Y') }}</td>
                        <td>
                            <form action="{{ route('admin.users.update-role', $user) }}" method="POST" class="d-inline">
                                @csrf
                                @method('PUT')
                                <select name="role" class="form-select form-select-sm d-inline w-auto" onchange="this.form.submit()">
                                    <option value="user" {{ $user->role === 'user' ? 'selected' : '' }}>Пользователь</option>
                                    <option value="editor" {{ $user->role === 'editor' ? 'selected' : '' }}>Редактор</option>
                                    <option value="admin" {{ $user->role === 'admin' ? 'selected' : '' }}>Администратор</option>
                                </select>
                            </form>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            {{ $users->links() }}
        </div>
    </div>
@endsection
