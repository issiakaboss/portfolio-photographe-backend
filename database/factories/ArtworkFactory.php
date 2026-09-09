<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

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
        ];
        static $index = 0;
        $selectedImage = $images[$index];
        $index = ($index + 1) % count($images);

        return [
            'title' => $this->faker->sentence(4),
            'description' => $this->faker->paragraph(),
            'image_path' => $selectedImage,
            'category' => $this->faker->randomElement(['Paysage', 'Portrait', 'Studio', 'Street Art']),
            'is_private' => false,
        ];
    }
}
