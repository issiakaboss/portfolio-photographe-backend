<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Storage;

class ArtworkFactory extends Factory
{
    public function definition(): array
    {
        // Liste de tes images dans l'ordre souhaité
        static $images = [
            'artworks/photo-1.jpg',
            'artworks/photo-2.jpg',
            'artworks/photo-3.jpg',
            'artworks/photo-4.jpg',
            'artworks/photo-5.jpg',
            'artworks/photo-6.jpg',
            'artworks/photo-7.jpg',
            'artworks/photo-8.jpg',
            'artworks/photo-9.jpg',
            'artworks/photo-10.jpg',

        ];
        static $index = 0;
        $selectedImage = collect([
            $images[$index],
            preg_replace('/\.jpg$/', '.webp', $images[$index]),
        ])->first(fn(?string $path): bool => $path !== null && Storage::disk('public')->exists($path))
            ?? $images[$index];
        $index = ($index + 1) % count($images);

        return [
            'title' => $this->faker->sentence(4),
            'description' => $this->faker->paragraph(),
            'image_path' => $selectedImage,
            'category' => $this->faker->randomElement(['photos', 'crafts', 'paintings', 'projects']),
            'price' => $this->faker->optional()->randomFloat(2, 25, 2500),
            'is_for_sale' => true,
            'status' => 'available',
            'dimensions' => $this->faker->optional()->randomElement(['30x40 cm', '50x70 cm', '80x120 cm']),
            'materials' => $this->faker->optional()->randomElement(['Papier fine art', 'Huile sur toile, cadre bois', 'Bois recycle']),
            'is_private' => false,
        ];
    }
}
