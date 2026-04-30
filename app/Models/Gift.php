<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Gift extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'icon', 'price', 'color', 'sort_order', 'is_active'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'is_active' => 'boolean',
        'sort_order' => 'integer'
    ];

    // Связь: подарки, которые отправили
    public function sentGifts()
    {
        return $this->hasMany(GiftUser::class);
    }
}
