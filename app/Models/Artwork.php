<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

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

    /**
     * Nettoyage automatique du fichier sur le disque lors de la suppression de l'œuvre.
     */
    protected static function booted()
    {
        static::deleting(function (Artwork $artwork) {
            if ($artwork->image_path) {
                $disk = $artwork->is_private ? 'private' : 'public';
                Storage::disk($disk)->delete($artwork->image_path);
            }
        });
    }
}
