<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        User::firstOrCreate(
            ['email' => 'admin@studiovisuals.com'],
            [
                'name' => 'Administrateur',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );

        $this->call([ArtworkSeeder::class, SiteContentSeeder::class]);
    }
}
