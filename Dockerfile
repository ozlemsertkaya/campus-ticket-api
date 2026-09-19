FROM php:8.3-cli

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

# Önce sadece bağımlılık dosyalarını al (önbellek için)
COPY composer.json composer.lock ./

# Zip arşivleri yerine doğrudan kaynaktan indir ve platform kontrolünü esnet
RUN composer install --no-dev --prefer-source --no-interaction --no-scripts --ignore-platform-reqs

# Kalan tüm dosyaları kopyala
COPY . .

EXPOSE 8080
CMD php artisan migrate --force && php artisan serve --host 0.0.0.0 --port ${PORT:-8080}