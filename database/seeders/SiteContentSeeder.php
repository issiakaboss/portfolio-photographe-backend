<?php

namespace Database\Seeders;

use App\Models\SiteContent;
use Illuminate\Database\Seeder;

class SiteContentSeeder extends Seeder
{
    public function run(): void
    {
        $contents = [
            ['section' => 'about', 'locale' => 'fr', 'content' => ['eyebrow' => "L'artiste", 'title' => 'Capturer des histoires qui traversent le temps.', 'bio' => "Passionné par l'image et le cadrage, j'explore la frontière entre l'esthétique brute et l'émotion pure à travers la photographie et la vidéographie professionnelle.", 'image_url' => 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?q=80&w=1000&auto=format&fit=crop', 'experience_years' => '5+', 'projects_completed' => '120+', 'experience_label' => "Années d'expérience", 'projects_label' => 'Projets réalisés']],
            ['section' => 'about', 'locale' => 'en', 'content' => ['eyebrow' => 'The creator', 'title' => 'Capturing stories that last forever.', 'bio' => 'Passionate about imagery and framing, I explore the boundary between raw aesthetics and pure emotion through professional photography and videography.', 'image_url' => 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?q=80&w=1000&auto=format&fit=crop', 'experience_years' => '5+', 'projects_completed' => '120+', 'experience_label' => 'Years of experience', 'projects_label' => 'Completed projects']],
            ['section' => 'contact', 'locale' => 'fr', 'content' => ['title' => 'Travaillons ensemble', 'description' => 'Un projet en tête ? Envoyez un message ou rejoignez-nous sur les réseaux.', 'panel_title' => 'Réseaux & Direct', 'panel_description' => 'Retrouvez nos réalisations et contactez-nous directement via vos plateformes favorites.', 'email' => 'bonjour@studiovisuals.fr', 'phone' => '+33 6 00 00 00 00', 'socials' => [['label' => 'Instagram Portfolio', 'url' => 'https://instagram.com'], ['label' => 'LinkedIn professionnel', 'url' => 'https://linkedin.com'], ['label' => 'WhatsApp direct', 'url' => 'https://wa.me/']]]],
            ['section' => 'contact', 'locale' => 'en', 'content' => ['title' => 'Let’s work together', 'description' => 'Got a project in mind? Send a message or connect via social networks.', 'panel_title' => 'Follow & connect', 'panel_description' => 'Discover our work and contact us directly through your favorite platforms.', 'email' => 'hello@studiovisuals.com', 'phone' => '+33 6 00 00 00 00', 'socials' => [['label' => 'Instagram portfolio', 'url' => 'https://instagram.com'], ['label' => 'Professional LinkedIn', 'url' => 'https://linkedin.com'], ['label' => 'Direct WhatsApp', 'url' => 'https://wa.me/']]]],
        ];

        foreach ($contents as $content) {
            SiteContent::updateOrCreate(['section' => $content['section'], 'locale' => $content['locale']], ['content' => $content['content']]);
        }
    }
}
