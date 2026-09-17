<?php

namespace Database\Seeders;

use App\Models\Artwork;
use App\Services\ArtworkMediaService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class ArtworkSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $disk = Storage::disk('public');
        $disk->deleteDirectory('thumbnails');
        $disk->delete('artworks/01M1GZD68TNATBPGGY5P9K34KJ.mp4');

        $mediaService = app(ArtworkMediaService::class);
        foreach ($disk->files('artworks') as $path) {
            if (str_starts_with(basename($path), 'photo-')) {
                $mediaService->optimize($path);
            }
        }

        $artworks = Artwork::factory()->count(10)->create();

        foreach ($artworks as $artwork) {
            $mediaService->thumbnailUrl($artwork->image_path);
        }
    }
}
