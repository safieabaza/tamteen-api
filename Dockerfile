FROM php:8.2-cli

# تثبيت extensions
RUN apt-get update && apt-get install -y \
    unzip \
    curl \
    git \
    libzip-dev \
    zip \
    && docker-php-ext-install pdo pdo_mysql

# تثبيت composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

# نسخ المشروع
COPY . .

# تثبيت dependencies
RUN composer install --no-dev --optimize-autoloader

# Laravel permissions
RUN chmod -R 777 storage bootstrap/cache

# تشغيل السيرفر
CMD php artisan serve --host=0.0.0.0 --port=10000