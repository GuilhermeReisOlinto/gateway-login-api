FROM php:8.2-fpm

RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libzip-dev \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libpq-dev \
    librdkafka-dev \
    && docker-php-ext-install \
    pdo_pgsql \
    mbstring \
    exif \
    pcntl \
    bcmath \
    gd \
    zip

RUN pecl install rdkafka \
    && docker-php-ext-enable rdkafka

COPY --from=composer:latest  /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

COPY . . 

RUN composer install --no-dev --optimize-autoloader

RUN chown -R www-data:www-data /var/www

EXPOSE 9000

CMD ["php-fpm"]