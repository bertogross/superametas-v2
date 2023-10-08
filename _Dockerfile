# Use the official PHP image as the base image
FROM php:8.1-apache

ARG PROJECT_FOLDER=superametas

WORKDIR /var/www/html/${PROJECT_FOLDER}

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    zip \
    unzip \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    libmcrypt-dev \
    nano

RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath opcache zip

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

CMD ["php-fpm"]

EXPOSE 9000
