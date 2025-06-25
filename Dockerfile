FROM php:8.4-cli-alpine

RUN apk add --no-cache \
    zip unzip curl libpq-dev git \
    && docker-php-ext-install pdo pdo_pgsql

WORKDIR /var/www

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

COPY . .

RUN composer install

EXPOSE 8000
