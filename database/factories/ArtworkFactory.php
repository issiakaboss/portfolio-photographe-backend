<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ArtworkFactory extends Factory
{
    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence(4),
            'description' => $this->faker->paragraph(),
            'image_path' => 'artworks/placeholder.jpg', 
            'category' => $this->faker->randomElement(['Paysage', 'Portrait', 'Studio', 'Street Art']),
            'is_private' => false,
        ];
    }
}