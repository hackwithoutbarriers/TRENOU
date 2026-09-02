# Déploiement TRENOU

## Pré-requis

- PHP 8.4.1+
- Composer
- Node.js 18+
- Base de données SQLite, MySQL ou PostgreSQL
- Accès SSH ou panneau du fournisseur d’hébergement

## Fichiers et dossiers déployables

Pour un déploiement, le point d’entrée est la racine du projet Laravel. Les éléments essentiels à inclure dans le package de publication sont :

- `app/` : contrôleurs, modèles, providers, services, ressources Filament
- `bootstrap/` : initialisation Laravel
- `config/` : configuration de l’application et services externes
- `database/` : migrations, seeders, factories
- `public/` : point d’entrée web ; contient aussi les assets compilés après `npm run build`
- `resources/` : vues Blade, CSS, JS source
- `routes/` : définitions des routes web et API
- `storage/` : répertoire d’uploads et cache runtime (à créer/avec permissions)
- `vendor/` : dépendances Composer (générées ou installées sur le serveur)
- `artisan` : interface Laravel CLI
- `.env.example` : référence de configuration
- `composer.json`, `composer.lock` : dépendances PHP
- `package.json`, `package-lock.json` : dépendances front-end
- `vite.config.js` : configuration Vite
- `phpunit.xml` : configuration des tests

Ne pas téléverser / ne pas inclure dans le paquet final :

- `.git/`, `.github/`, `.idea/`, `.vscode/`
- `node_modules/` (généré localement)
- `storage/logs/*` (logs locaux)
- `storage/framework/cache/*` et `storage/framework/views/*` (reconstruits sur serveur)
- `.env` (ou `copy .env.example` puis générer le `.env` sur le serveur)

## Production (Render)

1. Connectez le dépôt GitHub à Render et choisissez **Docker** comme runtime.
2. Laissez Render construire automatiquement l’image avec le [Dockerfile](./Dockerfile) à la racine.
3. Ne renseignez pas de build command Node : Composer et NPM sont exécutés dans les étapes Docker.
4. Si Render demande une commande de démarrage, utilisez `apache2-foreground`.
5. Configurez `APP_KEY`, `APP_ENV=production`, `APP_DEBUG=false`, `APP_URL`, la base de données, le mail et les autres variables nécessaires.
6. Après une modification du `Dockerfile` ou de `composer.lock`, utilisez **Clear build cache & deploy**.

Exécutez les migrations depuis un shell Render après le premier déploiement :

```bash
php artisan migrate --force
```

## Vérification finale

```bash
php artisan optimize
php artisan about
```

Assurez-vous que le panneau d’administration est accessible sur `/admin` et que les assets compilés sont présents dans `public/build`.
