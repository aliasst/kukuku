<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GiftUser extends Model
{
    use HasFactory;

    protected $table = 'gift_user';

    // Статусы
    const STATUS_PENDING = 'pending';   // Ожидает получения
    const STATUS_ACCEPTED = 'accepted'; // Получен
    const STATUS_IGNORED = 'ignored';   // Отклонён

    protected $fillable = [
        'gift_id', 'from_user_id', 'to_user_id', 'event_id', 'message', 'status'
    ];

    protected $casts = [
        'price' => 'decimal:2',
    ];

    // Связи
    public function gift()
    {
        return $this->belongsTo(Gift::class);
    }

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
            self::STATUS_ACCEPTED => 'Получен',
            self::STATUS_IGNORED => 'Отклонён',
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

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($gift) {
            $gift->status = self::STATUS_PENDING;
        });
    }
}
