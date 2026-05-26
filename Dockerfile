# Stage 1: PHP-FPM
FROM php:8.2-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    libicu-dev \
    zip \
    unzip

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip intl

# Custom PHP ini (upload limits, memory, etc.)
COPY docker/8.2/php.ini /usr/local/etc/php/conf.d/zz-custom.ini

# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy composer files first for better caching
COPY composer.json composer.lock* ./

# Install PHP dependencies without scripts and autoloader first
RUN composer install --no-interaction --no-plugins --no-scripts --no-dev --prefer-dist

# Copy the rest of the application
COPY . /var/www/html

# Generate autoloader and run scripts now that files are present
RUN composer dump-autoload --optimize --no-dev

# Set permissions for Laravel
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

EXPOSE 9000

CMD ["php-fpm"]
