# Use official PHP + Apache
FROM php:8.1-apache

# Install system deps and PHP extensions
RUN apt-get update && apt-get install -y \
    git unzip libzip-dev libpng-dev zlib1g-dev libicu-dev \
    && docker-php-ext-install pdo_mysql zip intl mbstring gd \
    && a2enmod rewrite \
    && rm -rf /var/lib/apt/lists/*

# Install composer by copying from the composer image
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Copy project files
COPY . /var/www/html

# Run composer (adjust flags for dev or prod)
RUN composer install --no-interaction --prefer-dist --no-dev || true

# Ensure writable folder (CodeIgniter uses writable/)
RUN chown -R www-data:www-data /var/www/html/writable || true
RUN chmod -R 755 /var/www/html/writable || true

EXPOSE 80
CMD ["apache2-foreground"]
