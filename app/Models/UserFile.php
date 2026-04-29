<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class UserFile extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'file_path',
        'file_name',
        'file_type',
        'file_size',
        'category',
        'description'
    ];

    public function getDownloadUrlAttribute()
    {
        return Storage::disk('public')->url($this->file_path);
    }



}
