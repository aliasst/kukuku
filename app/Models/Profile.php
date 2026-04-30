<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Profile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'full_name',
        'birth_date',
        'gender',
        'phone',
        'telegram',
        'visit_purpose',
        'avatar',
    ];

    protected $casts = [
        'birth_date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getAvatarUrlAttribute()
    {
        if ($this->avatar && Storage::disk('public')->exists($this->avatar)) {
            return Storage::url($this->avatar);
        }
        return '/storage/img/ava.png';
    }

    public function getFormattedDateAttribute()
    {
        if($this->birth_date) {
            return $this->birth_date->format('d.m.Y');
        }

            return '';
    }
}
