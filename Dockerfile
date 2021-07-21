FROM php:8.0-alpine

COPY --from=composer /usr/bin/composer /usr/bin/composer

WORKDIR /app
