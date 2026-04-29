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
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
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
