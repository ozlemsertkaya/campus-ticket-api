FROM php:8.2-cli

RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libpq-dev \
    libzip-dev \
    zip \
    && docker-php-ext-install pdo pdo_pgsql zip

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

ENV COMPOSER_ALLOW_SUPERUSER=1

WORKDIR /var/www
COPY . .

RUN composer install --no-dev --optimize-autoloader --no-interaction --ignore-platform-reqs

EXPOSE 8080
CMD php artisan migrate --force && php artisan serve --host 0.0.0.0 --port ${PORT:-8080}