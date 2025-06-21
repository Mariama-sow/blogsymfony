FROM php:8.2-fpm

# Installe les dépendances système et extensions PHP
RUN apt-get update && apt-get install -y \
    git \
    zip \
    unzip \
    libzip-dev \
    && docker-php-ext-install pdo pdo_mysql

# Installe Composer globalement
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www
