# syntax=docker/dockerfile:1
# Compile Vite assets in a separate stage.
FROM node:24-alpine AS frontend
WORKDIR /app
COPY package.json package-lock.json ./
RUN npm ci
COPY resources ./resources
COPY public ./public
COPY vite.config.js ./
RUN npm run build
# Install production PHP dependencies with the extensions required by the lockfile.
FROM php:8.4-cli AS vendor
WORKDIR /app
RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        libfreetype6-dev \
        libcurl4-openssl-dev \
        libicu-dev \
        libjpeg62-turbo-dev \
        libonig-dev \
        libpng-dev \
        libpq-dev \
        libsqlite3-dev \
        libxml2-dev \
        libzip-dev \
        unzip \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j"$(nproc)" \
        bcmath \
        curl \
        exif \
        gd \
        intl \
        mbstring \
        pcntl \
        pdo_pgsql \
        pdo_mysql \
        pdo_sqlite \
        xml \
        zip \
    && rm -rf /var/lib/apt/lists/*
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
COPY composer.json composer.lock ./
RUN rm -rf vendor \
    && composer install \
        --no-interaction \
        --no-dev \
        --prefer-dist \
        --no-autoloader \
        --no-scripts
# Le code source doit être présent AVANT de générer la classmap : avec
# --classmap-authoritative, Composer fige la liste des classes trouvées dans
# app/ à cet instant précis. S'il ne copie que composer.json/composer.lock
# avant cette étape, app/ est vide et aucune classe de l'application
# (contrôleurs, modèles, etc.) n'entre dans la classmap, d'où l'erreur
# "Target class ... does not exist" au runtime.
COPY . .
RUN composer dump-autoload \
        --no-dev \
        --classmap-authoritative \
        --no-interaction \
        --no-scripts \
    && php -r "require 'vendor/autoload.php';"
# Production Laravel image.
FROM php:8.4-apache
WORKDIR /var/www/html
RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        libfreetype6-dev \
        libcurl4-openssl-dev \
        libicu-dev \
        libjpeg62-turbo-dev \
        libwebp-dev \
        libonig-dev \
        libpng-dev \
        libpq-dev \
        libsqlite3-dev \
        libxml2-dev \
        libzip-dev \
        unzip \
    && rm -rf /var/lib/apt/lists/*
RUN docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp \
    && docker-php-ext-install -j"$(nproc)" \
        bcmath curl exif gd intl mbstring opcache pcntl pdo_pgsql pdo_mysql pdo_sqlite xml zip
# Configure Apache once. Do not edit both sites-available and sites-enabled:
# the latter contains symlinks to the same vhost files, so editing both applies
# the replacement twice and produces /var/www/html/public/public.
RUN a2enmod rewrite \
    && sed -ri 's#DocumentRoot /var/www/html$#DocumentRoot /var/www/html/public#g' \
        /etc/apache2/sites-available/*.conf \
    && sed -ri 's#<Directory /var/www/>#<Directory /var/www/html/public>#g' \
        /etc/apache2/apache2.conf \
    && printf '%s\n' \
        '<Directory /var/www/html/public>' \
        '    Options FollowSymLinks' \
        '    AllowOverride All' \
        '    Require all granted' \
        '</Directory>' \
        'DirectoryIndex index.php index.html' \
        > /etc/apache2/conf-available/laravel.conf \
    && a2enconf laravel \
    && apachectl -t
COPY --from=vendor /app/vendor ./vendor
COPY . .
COPY --from=frontend /app/public/build ./public/build
RUN test -f public/index.php
RUN mkdir -p \
        database \
        storage/app/public \
        storage/framework/cache \
        storage/framework/sessions \
        storage/framework/views \
        storage/logs \
    && rm -f bootstrap/cache/*.php \
    && chown -R www-data:www-data database storage bootstrap/cache \
    && find database -type d -exec chmod 775 {} \; \
    && find database -type f -exec chmod 664 {} \; \
    && printf '%s\n' \
        'opcache.enable=1' \
        'opcache.validate_timestamps=0' \
        'opcache.memory_consumption=128' \
        'opcache.max_accelerated_files=10000' \
        > /usr/local/etc/php/conf.d/opcache.ini \
    && php artisan package:discover --ansi
EXPOSE 80
# Configure APP_URL, database, mail and secrets in Render.
CMD ["sh", "-c", "php artisan storage:link --force && php artisan config:cache && php artisan route:cache && php artisan view:cache && apache2-foreground"]
