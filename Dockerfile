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
RUN composer dump-autoload \
        --no-dev \
        --classmap-authoritative \
        --no-interaction \
        --no-scripts \
    && php -r "require 'vendor/autoload.php';"

# Production Laravel image.
FROM php:8.4-apache

ENV APACHE_DOCUMENT_ROOT=/var/www/html/public

WORKDIR /var/www/html

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
        opcache \
        pcntl \
        pdo_pgsql \
        pdo_mysql \
        pdo_sqlite \
        xml \
        zip \
    && a2enmod rewrite \
    && sed -ri "s!DocumentRoot /var/www/html!DocumentRoot ${APACHE_DOCUMENT_ROOT}!g" \
        /etc/apache2/sites-available/*.conf \
        /etc/apache2/sites-enabled/*.conf \
    && sed -ri "s!<Directory /var/www/>!<Directory ${APACHE_DOCUMENT_ROOT}>!g" \
        /etc/apache2/apache2.conf \
    && rm -rf /var/lib/apt/lists/*

COPY --from=vendor /app/vendor ./vendor
COPY . .
COPY --from=frontend /app/public/build ./public/build

RUN mkdir -p \
        storage/app/public \
        storage/framework/cache \
        storage/framework/sessions \
        storage/framework/views \
        storage/logs \
    && chown -R www-data:www-data storage bootstrap/cache \
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
