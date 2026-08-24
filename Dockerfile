FROM php:8.2-fpm

# Установка зависимостей PHP
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpq-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    zip \
    autoconf \
    nano \
    vim \
    sudo \
    unzip

# Установка PHP расширений (добавляем pgsql и pdo_pgsql)
RUN docker-php-ext-install pdo_pgsql pgsql mbstring zip

# Установка Redis
RUN pecl install redis && docker-php-ext-enable redis

# Установка Xdebug
RUN pecl install xdebug && docker-php-ext-enable xdebug

# Копируем конфиг Xdebug специально для PHP-FPM
COPY docker/php/xdebug.ini /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

# Установка Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

EXPOSE 9000

CMD ["php-fpm"]
