FROM php:8.2-fpm

# Instala dependências do sistema
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libzip-dev \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libpq-dev \
    libssl-dev \
    libcurl4-openssl-dev \
    libc-ares-dev \
    libbrotli-dev \
    libzstd-dev \
    && docker-php-ext-install \
    pdo_pgsql \
    mbstring \
    exif \
    pcntl \
    bcmath \
    gd \
    zip

# Instala o Swoole
RUN pecl install swoole \
    && docker-php-ext-enable swoole

# Instala o Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Define o diretório de trabalho
WORKDIR /var/www

# Copia o código da aplicação
COPY . .

# Instala as dependências do Composer
RUN composer install --no-dev --optimize-autoloader

# Ajusta as permissões
RUN chown -R www-data:www-data /var/www

# Expõe a porta 9000
EXPOSE 9000

# Comando para iniciar o PHP-FPM
CMD ["php-fpm"]