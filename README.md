# TRENOU — ETS ALU LA SOLUTION

Application web de présentation et de gestion commerciale pour **ETS ALU LA
SOLUTION**, entreprise de menuiserie aluminium basée à Lomé, au Togo.

Le site permet de présenter les services et les réalisations, de recevoir des
demandes de devis et des messages de contact, puis de gérer ces données depuis
un panneau d’administration Filament.

## Fonctionnalités

### Site public

- Page d'accueil optimisée pour le référencement naturel.
- Présentation des services et pages détaillées par service.
- Galerie de projets administrables.
- Formulaire de demande de devis avec configuration des besoins.
- Formulaire de contact.
- Section avis clients, affichée lorsqu'elle contient des avis publiés.
- Partage d'un avis client.
- Métadonnées SEO, Open Graph, données structurées JSON-LD et URLs canoniques.
- `robots.txt` et `sitemap.xml` générés par Laravel.

### Administration

Le panneau est accessible à l'adresse `/admin` après authentification.

- Gestion des projets de la galerie.
- Lecture et suivi des messages de contact.
- Gestion des demandes de devis publiques et internes.
- Gestion des témoignages et avis.
- Gestion des attestations, certificats et documents PDF.
- Gestion des utilisateurs administrateurs.
- Profil administrateur : identité, mot de passe et authentification à deux
  facteurs.

## Stack technique

- PHP 8.4+
- Laravel 13
- Filament 3
- Livewire 3
- Tailwind CSS 4
- Vite 8
- SQLite en local, PostgreSQL recommandé en production
- Stockage local ou compatible S3/R2
- Browsershot, Puppeteer et Chromium pour les PDF
- PHPUnit pour les tests

## Prérequis

Pour une installation locale :

- PHP 8.4 ou supérieur avec les extensions utilisées par Laravel et GD WebP.
- Composer.
- Node.js et npm.
- Chromium si la génération de PDF est utilisée hors Docker.
- SQLite, MySQL ou PostgreSQL.

Les versions et extensions nécessaires au déploiement Docker sont définies dans
le [Dockerfile](./Dockerfile).

## Installation locale

Depuis la racine du projet :

```bash
composer install
copy .env.example .env
php artisan key:generate
php artisan migrate
npm install
npm run build
php artisan storage:link
```

Sous Linux ou macOS, remplacez `copy` par :

```bash
cp .env.example .env
```

Configurez ensuite les variables nécessaires dans `.env`, notamment :

```dotenv
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000
APP_LOCALE=fr

DB_CONNECTION=sqlite
DB_DATABASE=database/database.sqlite

FILESYSTEM_DISK=public
SESSION_DRIVER=database
QUEUE_CONNECTION=database
```

Pour SQLite, créez le fichier si nécessaire :

```bash
php -r "file_exists('database/database.sqlite') || touch('database/database.sqlite');"
php artisan migrate
```

## Lancer l'application

Serveur Laravel :

```bash
php artisan serve
```

Compilation Vite en mode développement :

```bash
npm run dev
```

Le site est alors disponible sur `http://localhost:8000`.

Pour lancer les processus prévus par le script Composer du projet :

```bash
composer run dev
```

## Génération des PDF

Les devis, attestations et certificats sont générés avec Browsershot,
Puppeteer et Chromium.

En local, Chromium doit être disponible dans le `PATH`. Si nécessaire,
configurez son emplacement :

```dotenv
BROWSERSHOT_CHROME_PATH=C:\chemin\vers\chromium.exe
BROWSERSHOT_NODE_BINARY=C:\chemin\vers\node.exe
BROWSERSHOT_NODE_MODULE_PATH=C:\chemin\vers\TRENOU\node_modules
```

Le Dockerfile installe Chromium et configure automatiquement les variables
nécessaires à Browsershot.

Les routes PDF sont protégées par authentification :

- `/devis/{devis}/pdf`
- `/attestations/{attestation}/pdf`
- `/attestations/{attestation}/certificat.pdf`
- `/attestations/{attestation}/documents`

## Tests et qualité du code

Lancer toute la suite PHPUnit :

```bash
php artisan test --compact
```

Lancer un fichier de test ciblé :

```bash
php artisan test --compact tests/Feature/PublicSiteTest.php
```

Formater les fichiers PHP modifiés avec Laravel Pint :

```bash
vendor/bin/pint --dirty --format agent
```

Construire les assets frontend :

```bash
npm run build
```

## Commandes Laravel utiles

```bash
php artisan migrate --force
php artisan db:seed --class=ProjetSeeder --force
php artisan storage:link --force
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan route:list
```

Le seeder `ProjetSeeder` est idempotent : il complète les projets manquants
sans écraser les projets déjà modifiés depuis l'administration.

## Déploiement Docker / Render

Le projet est prévu pour être déployé avec le [Dockerfile](./Dockerfile).
L'image :

1. compile les assets Vite ;
2. installe les dépendances PHP de production ;
3. installe Apache, Chromium et les extensions PHP nécessaires ;
4. configure Apache pour servir `public/` ;
5. crée les répertoires Laravel nécessaires ;
6. exécute les migrations, le seeder des projets et les caches Laravel au
   démarrage.

Dans Render, utilisez le runtime Docker et configurez les variables
d'environnement dans le tableau de bord. Ne versionnez jamais `.env`.

Variables de production importantes :

```dotenv
APP_ENV=production
APP_DEBUG=false
APP_URL=https://trenou.onrender.com
APP_KEY=...

DB_CONNECTION=pgsql
DB_HOST=...
DB_PORT=5432
DB_DATABASE=...
DB_USERNAME=...
DB_PASSWORD=...

FILESYSTEM_DISK=s3
AWS_ACCESS_KEY_ID=...
AWS_SECRET_ACCESS_KEY=...
AWS_DEFAULT_REGION=auto
AWS_BUCKET=...
AWS_ENDPOINT=...
```

Un stockage PostgreSQL et S3/R2 est recommandé en production, car le système
de fichiers d'un service Render gratuit peut être éphémère. Les images de
projets et les autres fichiers persistants doivent donc être stockés sur un
volume ou un service objet durable.

Après un déploiement ou une modification de configuration :

```bash
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

Vérifiez ensuite :

- `/`
- `/admin`
- `/robots.txt`
- `/sitemap.xml`
- `/galerie`
- `/contact`
- `/devis`

## SEO

Les éléments SEO sont centralisés dans :

- [resources/views/public/layout.blade.php](resources/views/public/layout.blade.php)
- [app/Http/Controllers/SeoController.php](app/Http/Controllers/SeoController.php)
- [resources/views/seo/sitemap.blade.php](resources/views/seo/sitemap.blade.php)

Le domaine canonique doit être défini par `APP_URL`. Après la mise en
production, soumettez le sitemap à Google Search Console et Bing Webmaster
Tools.

## Structure principale

```text
app/
  Filament/          Ressources et pages de l'administration
  Http/              Contrôleurs et Form Requests
  Models/            Modèles Eloquent
  Services/          Services métier et génération PDF
bootstrap/           Démarrage et cache Laravel
config/              Configuration de l'application
database/
  migrations/        Schéma de base de données
  seeders/           Données initiales
public/              Point d'entrée web et assets compilés
resources/
  css/               Styles Tailwind
  js/                JavaScript frontend
  views/              Vues Blade publiques, admin et PDF
routes/              Routes web et console
storage/             Fichiers runtime et uploads
tests/               Tests PHPUnit
```

## Sécurité et bonnes pratiques

- Ne jamais committer `.env`, des mots de passe ou des tokens.
- Garder `APP_DEBUG=false` en production.
- Régénérer les secrets s'ils ont été exposés.
- Ne pas exposer directement `storage/`, les logs ou les caches.
- Conserver les routes PDF derrière l'authentification.
- Utiliser HTTPS pour `APP_URL` en production.
- Utiliser un stockage persistant pour les uploads de production.

## Licence

Le code de l'application est distribué selon les conditions définies par le
propriétaire du projet. Les dépendances conservent leurs propres licences.
