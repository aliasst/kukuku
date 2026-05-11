<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wink extends Model
{
    use HasFactory;

    // Статусы
    const STATUS_PENDING = 'pending';   // Ожидает ответа
    const STATUS_ACCEPTED = 'accepted'; // Подтверждено (взаимно)
    const STATUS_IGNORED = 'ignored';   // Проигнорировано

    protected $fillable = [
        'from_user_id',
        'to_user_id',
        'event_id',
        'is_viewed',
        'status'
    ];

    protected $casts = [
        'is_viewed' => 'boolean',
    ];

    // Связи
    public function fromUser()
    {
        return $this->belongsTo(User::class, 'from_user_id');
    }

    public function toUser()
    {
        return $this->belongsTo(User::class, 'to_user_id');
    }

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    // Аксессоры
    public function getStatusNameAttribute()
    {
        return match($this->status) {
            self::STATUS_PENDING => 'Ожидает',
            self::STATUS_ACCEPTED => 'Принято',
            self::STATUS_IGNORED => 'Отклонено',
            default => 'Неизвестно',
        };
    }

    public function getStatusBadgeClassAttribute()
    {
        return match($this->status) {
            self::STATUS_PENDING => 'bg-warning',
            self::STATUS_ACCEPTED => 'bg-success',
            self::STATUS_IGNORED => 'bg-secondary',
            default => 'bg-dark',
        };
    }

    // Проверка, есть ли взаимное подмигивание
    public function isMutual()
    {
        return self::where('from_user_id', $this->to_user_id)
            ->where('to_user_id', $this->from_user_id)
            ->where('event_id', $this->event_id)
            ->where('status', self::STATUS_ACCEPTED)
            ->exists();
    }

    // Статистика
    public static function getStats($userId)
    {
        return [
            'sent' => self::where('from_user_id', $userId)->count(),
            'received' => self::where('to_user_id', $userId)->count(),
            'pending' => self::where('to_user_id', $userId)->where('status', self::STATUS_PENDING)->count(),
            'accepted' => self::where('to_user_id', $userId)->where('status', self::STATUS_ACCEPTED)->count(),
            'ignored' => self::where('to_user_id', $userId)->where('status', self::STATUS_IGNORED)->count(),
        ];
    }

    // Получить непросмотренные подмигивания для пользователя
    public static function getUnviewedCount($userId)
    {
        return self::where('to_user_id', $userId)
            ->where('is_viewed', false)
            ->where('status', self::STATUS_PENDING)
            ->count();
    }

    // Автоматическая установка статуса при создании
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($wink) {
            $wink->status = self::STATUS_PENDING;
            $wink->is_viewed = false;
        });
    }
}
