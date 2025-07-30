# Use PHP with Composer installed
FROM php:8.2-apache

# Install dependencies required for Composer (zip, unzip)
RUN apt-get update && apt-get install -y unzip zip

# Install Composer globally
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www

# Copy application code
COPY public/ /var/www/html/
COPY src/ /var/www/src/

# Copy composer files and install dependencies
COPY composer.json composer.lock ./
RUN composer install --no-dev --optimize-autoloader

# Permissions and Apache setup
RUN chown -R www-data:www-data /var/www/html /var/www/src
RUN a2enmod rewrite

# Expose port 80
EXPOSE 80

# Start Apache
CMD ["apache2-foreground"]

