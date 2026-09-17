# Contexte de travail pour Gemini - Backend

## 1. Objectif du depot

Ce depot contient l'API et le back-office du portfolio `Studio Visuals`. Il fournit les donnees au frontend Astro situe dans `portfolio-photographe-frontend` et permet de gerer les oeuvres avec Filament.

Le backend expose actuellement :

- les oeuvres publiques du portfolio ;
- le detail d'une oeuvre ;
- les temoignages approuves et leur creation publique ;
- le contenu CMS des pages About et Contact, par locale.

## 2. Stack et commandes

- PHP `^8.3`
- Laravel `^13.17`
- Filament `~5.0`
- Laravel Sanctum `^4.0`
- PHPUnit `^12.5`
- Laravel Pint
- Vite et Tailwind pour les assets eventuels du back-office

Commandes utiles depuis la racine :

```bash
php -v
composer -V
composer install
php artisan serve
php artisan migrate
php artisan db:seed
php artisan route:list
php artisan test
vendor/bin/pint
npm install
npm run build
```

Le frontend de developpement attend normalement l'API sur `http://127.0.0.1:8000`.

## 3. Structure utile

```text
app/
  Filament/Resources/Artworks/       CRUD Filament des oeuvres
  Http/Controllers/Api/              Controllers API publics
  Http/Resources/                     Transformations JSON
  Models/                             Artwork, Testimonial, SiteContent...
  Services/                           Gestion des medias
bootstrap/app.php                    Configuration Laravel et erreurs JSON API
routes/api.php                       Routes publiques de l'API
database/migrations/                 Schema de la base
database/seeders/                    Donnees de demonstration
resources/                            Assets et vues Laravel
config/filesystems.php               Disques public/private
```

L'administration est basee sur Filament. La resource importante ici est `app/Filament/Resources/Artworks/ArtworkResource.php`.

## 4. Routes API actuelles

Toutes les routes ci-dessous sont dans `routes/api.php`, donc prefixees par `/api`.

| Methode | Route | Controleur | Usage |
|---|---|---|---|
| GET | `/artworks` | `ArtworkController@index` | Liste publique, tri decroissant par creation |
| GET | `/artworks/{id}` | `ArtworkController@show` | Detail d'une oeuvre publique |
| GET | `/testimonials` | `TestimonialController@index` | Temoignages approuves |
| POST | `/testimonials` | `TestimonialController@store` | Creation publique d'un temoignage |
| GET | `/content/{section}` | `SiteContentController@show` | Contenu `about` ou `contact` selon `locale` |

`GET /user` existe aussi et est protege par Sanctum, mais il n'est pas utilise par le frontend public actuel.

## 5. Contrats de donnees

### Artwork

Table `artworks` :

- `id`
- `title` string obligatoire
- `description` texte nullable
- `image_path` string obligatoire, stocke en base
- `category` string, defaut `general`
- `is_private` boolean, defaut `false`
- `access_token` nullable, reserve au futur acces prive
- timestamps

`ArtworkResource` renvoie :

```json
{
  "id": 1,
  "title": "Titre",
  "description": "Description ou null",
  "image_url": "/storage/artworks/fichier.jpg ou null",
  "thumbnail_url": "URL ou null",
  "category": "general",
  "is_private": false,
  "created_at": "2026-09-06T12:00:00+00:00"
}
```

`ArtworkController@index` filtre `is_private = false`, trie avec `latest()` et limite la valeur de `limit` a 24. `show` refuse aussi les oeuvres privees.

Le media est uploadable comme image, MP4, QuickTime ou WebM via Filament. Les fichiers publics sont sur le disque public dans `artworks/`. Le frontend utilise `/storage/...` et le lien symbolique `public/storage`.

### Testimonial

Table `testimonials` :

- `author` string obligatoire
- `role` nullable
- `quote` texte
- `avatar` nullable
- `is_approved` boolean, defaut `true`
- timestamps

La liste ne renvoie que `is_approved = true`.

Le POST valide : `author` max 255, `role` nullable max 255 et `quote` max 500. Il cree aussi un avatar Unsplash aleatoire et force `is_approved = true`.

### SiteContent

Table `site_contents` :

- `section`
- `locale` max 5
- `content` JSON cast en tableau
- timestamps
- contrainte unique `section + locale`

Sections acceptees par l'API : uniquement `about` et `contact`.

Shape actuelle `about` :

```json
{
  "eyebrow": "...",
  "title": "...",
  "bio": "...",
  "image_url": "https://...",
  "experience_years": "5+",
  "projects_completed": "120+",
  "experience_label": "...",
  "projects_label": "..."
}
```

Shape actuelle `contact` :

```json
{
  "title": "...",
  "description": "...",
  "panel_title": "...",
  "panel_description": "...",
  "email": "...",
  "phone": "...",
  "socials": [
    { "label": "...", "url": "https://..." }
  ]
}
```

## 6. Seeders et developpement

`DatabaseSeeder` cree notamment :

- un utilisateur de demonstration ;
- un administrateur `admin@studiovisuals.com` avec le mot de passe de demonstration present dans le seeder ;
- les seeders des oeuvres et contenus CMS.

Ne jamais reutiliser le mot de passe de demonstration en production et ne jamais ajouter de secret dans Git.

`ArtworkSeeder` cree 10 oeuvres via la factory. `SiteContentSeeder` cree les sections `about` et `contact` en `fr` et `en`.

## 7. Regles pour changer le format

Avant une refonte, documenter le nouveau contrat et decider :

1. migration additive ou rupture de compatibilite ;
2. nouvelle forme des enveloppes JSON (`data`, pagination, erreurs) ;
3. nommage des champs et types ;
4. gestion des locales et fallback ;
5. representation des medias, thumbnails et videos ;
6. gestion des contenus absents ;
7. authentification et moderation des endpoints d'ecriture.

Pour une modification de format :

- modifier les migrations et model casts si la base change ;
- modifier les resources/controllers plutot que de transformer le JSON dans plusieurs endroits ;
- mettre a jour les seeders, factories et Filament pour garder le back-office coherent ;
- mettre a jour les tests Feature API ;
- verifier CORS, stockage public/private et cache HTTP ;
- verifier le frontend Astro en parallele.

## 8. Points d'attention connus

- Le contenu CMS est du JSON libre : le backend ne valide pas actuellement la structure interne de `content`.
- Le POST `/testimonials` est public et auto-approuve. Une nouvelle version devrait probablement ajouter moderation, rate limiting et/ou anti-spam avant production.
- Les URLs media de `ArtworkResource` sont relatives (`/storage/...`), tandis que le CMS peut contenir des URLs absolues.
- `thumbnailUrl()` depend de `ArtworkMediaService`; verifier ce service avant de changer le format des thumbnails.
- La private storage et `access_token` existent dans le modele mais ne sont pas exposees par les routes publiques actuelles.
- Le frontend peut appeler les endpoints pendant le build statique. Une rupture de contrat API bloque donc directement le build.
- Les donnees d'exemple contiennent des URLs Unsplash et des valeurs de contact fictives : les traiter comme fixtures, pas comme donnees metier definitives.

## 9. Consignes pour l'agent qui reprend le projet

- Lire aussi `GEMINI_CONTEXT.md` du frontend avant de modifier une reponse API.
- Ne pas supprimer un champ sans verifier toutes les utilisations dans le frontend.
- Preferer une migration progressive avec compatibilite temporaire si le nouveau format n'est pas encore impose.
- Ajouter ou mettre a jour des tests Feature pour chaque contrat public modifie.
- Executer au minimum `php artisan route:list` et `php artisan test` apres une modification API.
- Si le « nouveau format » n'est pas fourni, demander un exemple JSON attendu avant d'implementer.
