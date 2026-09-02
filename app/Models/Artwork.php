<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Artwork extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'image_path',
        'category',
        'is_private',
        'access_token',
    ];

    protected $casts = [
        'is_private' => 'boolean',
    ];
}
