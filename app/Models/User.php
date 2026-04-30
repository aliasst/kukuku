<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    // Константы ролей
    const ROLE_ADMIN = 'admin';
    const ROLE_EDITOR = 'editor';
    const ROLE_USER = 'user';

    protected $fillable = [
        'name', 'email', 'password', 'role'
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    // Проверки ролей
    public function isAdmin()
    {
        return $this->role === self::ROLE_ADMIN;
    }

    public function isEditor()
    {
        return $this->role === self::ROLE_EDITOR;
    }

    public function isUser()
    {
        return $this->role === self::ROLE_USER;
    }

    public function hasRole($role)
    {
        return $this->role === $role;
    }

    public function hasAnyRole($roles)
    {
        return in_array($this->role, (array) $roles);
    }

    // Получить название роли
    public function getRoleNameAttribute()
    {
        return match($this->role) {
            self::ROLE_ADMIN => 'Администратор',
            self::ROLE_EDITOR => 'Редактор',
            self::ROLE_USER => 'Пользователь',
            default => 'Неизвестно',
        };
    }

    public function getRoleBadgeClassAttribute()
    {
        return match($this->role) {
            self::ROLE_ADMIN => 'bg-danger',
            self::ROLE_EDITOR => 'bg-warning text-dark',
            self::ROLE_USER => 'bg-secondary',
            default => 'bg-dark',
        };
    }


    // Генерация уникального токена для сброса пароля
    public function generatePasswordResetToken()
    {
        return Str::random(60);
    }

    public function profile()
    {
        return $this->hasOne(Profile::class);
    }

    // Документы пользователя (один ко многим)
    public function documents()
    {
        return $this->hasMany(UserDocument::class);
    }

    // Документы пользователя (один ко многим)
    public function files()
    {
        return $this->hasMany(UserFile::class);
    }

    public function getOrCreateProfile()
    {
        if ($this->profile === null) {
            $this->profile()->create([]);
        }
        return $this->profile;
    }

    // Связь с событиями через таблицу event_user
    public function events()
    {
        return $this->belongsToMany(Event::class, 'event_user')
            ->withPivot('id', 'status', 'ticket_number', 'registered_at', 'confirmed_at', 'attended_at', 'notes')
            ->withTimestamps();
    }

    // События, на которые пользователь зарегистрирован
    public function registeredEvents()
    {
        return $this->belongsToMany(Event::class, 'event_user')
            ->wherePivot('status', 'registered')
            ->withPivot('registered_at');
    }

    // События, в которых пользователь участвовал
    public function attendedEvents()
    {
        return $this->belongsToMany(Event::class, 'event_user')
            ->wherePivot('status', 'attended')
            ->withPivot('attended_at');
    }

    // События, которые пользователь подтвердил
    public function confirmedEvents()
    {
        return $this->belongsToMany(Event::class, 'event_user')
            ->wherePivot('status', 'confirmed');
    }

    // Проверить, зарегистрирован ли пользователь на событие
    public function isRegisteredForEvent($eventId)
    {
        return $this->events()->where('event_id', $eventId)->exists();
    }

    // Получить статус участия в событии
    public function getEventStatus($eventId)
    {
        $record = $this->events()->where('event_id', $eventId)->first();
        return $record ? $record->pivot->status : null;
    }
}
