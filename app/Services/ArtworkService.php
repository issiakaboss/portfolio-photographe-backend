<?php

namespace App\Services;

use App\Models\Artwork;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class ArtworkService
{
    /**
     * Gère l'enregistrement sécurisé d'une œuvre et de son image.
     */
    public function storeArtwork(array $data, UploadedFile $file): Artwork
    {
        $isPrivate = filter_var($data['is_private'] ?? false, FILTER_VALIDATE_BOOLEAN);
        $disk = $isPrivate ? 'private' : 'public';

        // Stockage du fichier sur le disque approprié
        $path = $file->store('artworks', $disk);

        // Enregistrement en base de données (uniquement les chemins/métadonnées)
        return Artwork::create([
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'image_path' => $path,
            'category' => $data['category'] ?? 'general',
            'is_private' => $isPrivate,
            'access_token' => $isPrivate ? bin2hex(random_bytes(16)) : null,
        ]);
    }
}