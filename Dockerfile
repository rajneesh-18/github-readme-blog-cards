# Use PHP with Composer installed
FROM php:8.2-apache

# Install dependencies required for Composer (zip, unzip)
RUN apt-get update && apt-get install -y unzip zip

# Install Composer globally
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www

# Copy composer files and install dependencies
COPY composer.json composer.lock ./
RUN composer install --no-dev --optimize-autoloader

# Copy application code
COPY public/ /var/www/html/
COPY src/ /var/www/src/

# Permissions and Apache setup
RUN chown -R www-data:www-data /var/www/html /var/www/src
RUN a2enmod rewrite

EXPOSE 80
