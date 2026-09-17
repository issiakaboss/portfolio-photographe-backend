<?php

namespace App\Models;

use App\Services\ArtworkMediaService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class Artwork extends Model
{
    use HasFactory;

    public const CATEGORIES = [
        'photos',
        'crafts',
        'paintings',
        'projects',
    ];

    public const STATUSES = [
        'available',
        'sold',
    ];

    protected $fillable = [
        'title',
        'description',
        'image_path',
        'category',
        'price',
        'is_for_sale',
        'status',
        'dimensions',
        'materials',
        'is_private',
        'access_token',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'is_for_sale' => 'boolean',
        'is_private' => 'boolean',
    ];

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Nettoyage automatique du fichier sur le disque lors de la suppression de l'œuvre.
     */
    protected static function booted()
    {
        static::saved(function (Artwork $artwork): void {
            $disk = $artwork->is_private ? 'private' : 'public';
            $optimizedPath = app(ArtworkMediaService::class)->optimize($artwork->image_path, $disk);

            if ($optimizedPath !== null && $optimizedPath !== $artwork->image_path) {
                $artwork->updateQuietly(['image_path' => $optimizedPath]);
            }
        });

        static::deleting(function (Artwork $artwork) {
            if ($artwork->image_path) {
                $disk = $artwork->is_private ? 'private' : 'public';
                Storage::disk($disk)->delete($artwork->image_path);
            }
        });
    }
}
