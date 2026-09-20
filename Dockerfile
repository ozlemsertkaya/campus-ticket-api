FROM php:8.3-cli

RUN apt-get update && apt-get install -y libpq-dev && docker-php-ext-install pdo pdo_pgsql

WORKDIR /var/www

COPY . .

EXPOSE 8080

CMD php artisan serve --host 0.0.0.0 --port 8080