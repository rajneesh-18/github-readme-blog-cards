# Use official PHP image with Apache
FROM php:8.2-apache

# Enable Apache mod_rewrite (optional, for clean URLs)
RUN a2enmod rewrite

# Copy public files into the Apache web root
COPY public/ /var/www/html/

# Copy source files (optional, if needed in runtime)
COPY src/ /var/www/src/

# Set correct permissions
RUN chown -R www-data:www-data /var/www/html /var/www/src

# Expose port 80
EXPOSE 80
