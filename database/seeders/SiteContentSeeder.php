<?php

namespace Database\Seeders;

use App\Models\SiteContent;
use Illuminate\Database\Seeder;

class SiteContentSeeder extends Seeder
{
    public function run(): void
    {
        $contents = [
            ['section' => 'about', 'locale' => 'fr', 'content' => ['eyebrow' => "Maison Phid'ou", 'title' => 'Un œil sur la rue, les marchés, la terre...', 'bio' => "Maison Phid'ou observe les gestes, les matières et les histoires qui naissent du quotidien. Une maison dédiée aux images, aux objets et aux projets qui portent une mémoire vivante.", 'image_url' => 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?q=80&w=1000&auto=format&fit=crop', 'experience_years' => 'Maison', 'projects_completed' => 'Créations', 'experience_label' => 'Une vision sensible', 'projects_label' => 'Des pièces singulières']],
            ['section' => 'about', 'locale' => 'en', 'content' => ['eyebrow' => "Maison Phid'ou", 'title' => 'An eye on the street, the markets, the earth...', 'bio' => "Maison Phid'ou observes gestures, materials and the stories born from everyday life. A house dedicated to images, objects and projects carrying a living memory.", 'image_url' => 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?q=80&w=1000&auto=format&fit=crop', 'experience_years' => 'Maison', 'projects_completed' => 'Creations', 'experience_label' => 'A sensitive vision', 'projects_label' => 'Singular pieces']],
            ['section' => 'contact', 'locale' => 'fr', 'content' => ['title' => 'Travaillons ensemble', 'description' => 'Un projet en tête ? Envoyez un message ou rejoignez-nous sur les réseaux.', 'panel_title' => 'Réseaux & Direct', 'panel_description' => 'Retrouvez nos réalisations et contactez-nous directement via vos plateformes favorites.', 'email' => 'bonjour@studiovisuals.fr', 'phone' => '+33 6 00 00 00 00', 'socials' => [['label' => 'Instagram Portfolio', 'url' => 'https://instagram.com'], ['label' => 'LinkedIn professionnel', 'url' => 'https://linkedin.com'], ['label' => 'WhatsApp direct', 'url' => 'https://wa.me/']]]],
            ['section' => 'contact', 'locale' => 'en', 'content' => ['title' => 'Let’s work together', 'description' => 'Got a project in mind? Send a message or connect via social networks.', 'panel_title' => 'Follow & connect', 'panel_description' => 'Discover our work and contact us directly through your favorite platforms.', 'email' => 'hello@studiovisuals.com', 'phone' => '+33 6 00 00 00 00', 'socials' => [['label' => 'Instagram portfolio', 'url' => 'https://instagram.com'], ['label' => 'Professional LinkedIn', 'url' => 'https://linkedin.com'], ['label' => 'Direct WhatsApp', 'url' => 'https://wa.me/']]]],
        ];

        foreach ($contents as $content) {
            SiteContent::updateOrCreate(['section' => $content['section'], 'locale' => $content['locale']], ['content' => $content['content']]);
        }
    }
}
