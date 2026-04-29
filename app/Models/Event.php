<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'description',
        'content',
        'event_date',
        'event_time',
        'event_end_date',
        'location',
        'price',
        'price_currency',
        'max_participants',
        'current_participants',
        'image',
        'type',
        'status',
        'is_free',
        'is_online',
        'online_link',
        'additional_info'
    ];

    protected $casts = [
        'event_date' => 'date',
        'event_end_date' => 'date',
        'event_time' => 'datetime',
        'price' => 'decimal:2',
        'is_free' => 'boolean',
        'is_online' => 'boolean',
        'max_participants' => 'integer',
        'current_participants' => 'integer',
        'additional_info' => 'array',
    ];

    // Константы типов событий
    const TYPE_UPCOMING = 'upcoming';   // Предстоящее
    const TYPE_PAST = 'past';           // Прошедшее
    const TYPE_ONGOING = 'ongoing';     // Идет сейчас
    const TYPE_CANCELLED = 'cancelled'; // Отменено

    // Константы статусов
    const STATUS_ACTIVE = 'active';
    const STATUS_COMPLETED = 'completed';
    const STATUS_CANCELLED = 'cancelled';

    // Boot метод
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($event) {
            if (empty($event->slug)) {
                $event->slug = Str::slug($event->title);
            }
            $event->type = $event->calculateType();
        });

        static::updating(function ($event) {
            $event->type = $event->calculateType();
        });
    }

    // Расчет типа события
    public function calculateType()
    {
        $now = now();

        if ($this->status === self::STATUS_CANCELLED) {
            return self::TYPE_CANCELLED;
        }

        $startDate = $this->event_date;
        $endDate = $this->event_end_date ?? $this->event_date;

        if ($endDate < $now) {
            return self::TYPE_PAST;
        }

        if ($startDate <= $now && $endDate >= $now) {
            return self::TYPE_ONGOING;
        }

        return self::TYPE_UPCOMING;
    }

    /**
     * ПОЛУЧИТЬ URL КАРТИНКИ СОБЫТИЯ
     * Если картинки нет - возвращает картинку по умолчанию
     */
    public function getImageUrlAttribute()
    {
        // Если картинка есть и она существует физически
        if ($this->image && Storage::disk('public')->exists($this->image)) {
            return Storage::url($this->image);
        }

        // Иначе возвращаем картинку по умолчанию
        return $this->getDefaultImage();
    }

    /**
     * Получить картинку по умолчанию (в зависимости от типа события)
     */
    public function getDefaultImage()
    {
        // Пути к дефолтным картинкам (можно создать свои)
        $defaultImages = [
            'upcoming' => 'img/event.png',
            'past' => 'img/event.png',
            'ongoing' => 'img/event.png',
            'cancelled' => 'img/event.png',
        ];

        $defaultPath = $defaultImages[$this->type] ?? 'img/event.png';

        // Проверяем, есть ли дефолтная картинка в storage
        if (Storage::disk('public')->exists($defaultPath)) {
            return Storage::url($defaultPath);
        }

        // Если нет файлов, возвращаем заглушку с градиентом или SVG
        return $this->getDefaultPlaceholder();
    }

    /**
     * Генератор SVG-заглушки (если нет файлов)
     */
    public function getDefaultPlaceholder()
    {
        $colors = [
            'upcoming' => '28a745',  // зеленый
            'past' => '6c757d',       // серый
            'ongoing' => 'ffc107',    // желтый
            'cancelled' => 'dc3545',  // красный
        ];

        $color = $colors[$this->type] ?? '667eea';
        $title = urlencode($this->title ?? 'Event');

        // Используем сервис ui-avatars для генерации красивой заглушки
        return "https://ui-avatars.com/api/?name={$title}&background={$color}&color=fff&size=400&fontsize=100&rounded=false";
    }

    /**
     * Проверить, есть ли своя картинка у события
     */
    public function hasImage()
    {
        return $this->image && Storage::disk('public')->exists($this->image);
    }

    /**
     * Альтернативный метод для получения картинки (без аксессора)
     */
    public function getImageUrl()
    {
        return $this->getImageUrlAttribute();
    }

    // Аксессоры
    public function getTypeNameAttribute()
    {
        return match($this->type) {
            self::TYPE_UPCOMING => 'Предстоящее',
            self::TYPE_PAST => 'Прошедшее',
            self::TYPE_ONGOING => 'Идет сейчас',
            self::TYPE_CANCELLED => 'Отменено',
            default => 'Неизвестно',
        };
    }





    public function getFormattedPriceAttribute()
    {
        if ($this->is_free || $this->price == 0) {
            return 'Бесплатно';
        }
        return number_format($this->price, 0, ',', ' ') . ' ' . 'Руб';
    }

    public function getFormattedDateAttribute()
    {
        return $this->event_date->format('d.m.Y');
    }



    public function getPlacesLeftAttribute()
    {
        if (!$this->max_participants) {
            return null;
        }
        return $this->max_participants - $this->current_participants;
    }

    public function getIsFullAttribute()
    {
        if (!$this->max_participants) {
            return false;
        }
        return $this->current_participants >= $this->max_participants;
    }

    public function getIsPastAttribute()
    {
        return $this->type === self::TYPE_PAST;
    }

    public function getIsUpcomingAttribute()
    {
        return $this->type === self::TYPE_UPCOMING;
    }

    public function getIsOngoingAttribute()
    {
        return $this->type === self::TYPE_ONGOING;
    }

    // Скоупы
    public function scopeUpcoming($query)
    {
        return $query->where('type', self::TYPE_UPCOMING)->orderBy('event_date', 'asc');
    }

    public function scopePast($query)
    {
        return $query->where('type', self::TYPE_PAST)->orderBy('event_date', 'desc');
    }

    public function scopeOngoing($query)
    {
        return $query->where('type', self::TYPE_ONGOING);
    }

    public function scopeFree($query)
    {
        return $query->where('is_free', true)->orWhere('price', 0);
    }

    public function scopePaid($query)
    {
        return $query->where('is_free', false)->where('price', '>', 0);
    }

    // Связь с пользователями через таблицу event_user
    public function users()
    {
        return $this->belongsToMany(User::class, 'event_user')
            ->withPivot('id', 'status', 'ticket_number', 'registered_at', 'confirmed_at', 'attended_at', 'notes')
            ->withTimestamps();
    }

    // Все пользователи, зарегистрированные на событие
    public function registeredUsers()
    {
        return $this->belongsToMany(User::class, 'event_user')
            ->wherePivot('status', 'registered')
            ->withPivot('registered_at', 'ticket_number');
    }

    // Пользователи, подтвердившие участие
    public function confirmedUsers()
    {
        return $this->belongsToMany(User::class, 'event_user')
            ->wherePivot('status', 'confirmed')
            ->withPivot('confirmed_at');
    }

    // Пользователи, которые приняли участие (посетили)
    public function attendedUsers()
    {
        return $this->belongsToMany(User::class, 'event_user')
            ->wherePivot('status', 'attended')
            ->withPivot('attended_at');
    }

    // Пользователи, которые отменили запись
    public function cancelledUsers()
    {
        return $this->belongsToMany(User::class, 'event_user')
            ->wherePivot('status', 'cancelled');
    }

    // Проверить, зарегистрирован ли пользователь
    public function isUserRegistered($userId)
    {
        return $this->users()->where('user_id', $userId)->exists();
    }

    // Проверить, участвовал ли пользователь
    public function isUserAttended($userId)
    {
        return $this->users()
            ->where('user_id', $userId)
            ->wherePivot('status', 'attended')
            ->exists();
    }

    // Получить статус участия пользователя
    public function getUserStatus($userId)
    {
        $record = $this->users()->where('user_id', $userId)->first();
        return $record ? $record->pivot->status : null;
    }

    // Получить количество зарегистрированных
    public function getRegisteredCountAttribute()
    {
        return $this->users()->wherePivot('status', 'registered')->count();
    }

    // Получить количество подтвердивших
    public function getConfirmedCountAttribute()
    {
        return $this->users()->wherePivot('status', 'confirmed')->count();
    }

    // Получить количество участников (кто реально пришел)
    public function getAttendedCountAttribute()
    {
        return $this->users()->wherePivot('status', 'attended')->count();
    }

    // Получить количество отменивших
    public function getCancelledCountAttribute()
    {
        return $this->users()->wherePivot('status', 'cancelled')->count();
    }

    // Обновить текущее количество участников
    public function updateCurrentParticipants()
    {
        $this->current_participants = $this->attended_count;
        $this->saveQuietly();
    }


}
