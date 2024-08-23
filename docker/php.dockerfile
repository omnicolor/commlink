FROM php:8.3-fpm
COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer
RUN apt-get update \
    && apt-get install -y --no-install-recommends libzip-dev libcurl4-openssl-dev pkg-config libssl-dev \
    && docker-php-ext-install pdo_mysql zip \
    && pecl install mongodb-1.19.3 redis-6.0.2 xdebug-3.3.2 \
    && docker-php-ext-enable mongodb redis xdebug
