# Use PHP 8.1 with Apache web server
FROM php:8.1-apache

# Install system dependencies needed for SQLite
RUN apt-get update && apt-get install -y libsqlite3-dev && \
    docker-php-ext-install pdo pdo_mysql pdo_sqlite mysqli

# Enable PHP output buffering globally to prevent "headers already sent" errors
COPY php-custom.ini /usr/local/etc/php/conf.d/custom.ini

# Enable Apache mod_rewrite for clean URLs
RUN a2enmod rewrite

# Set the working directory
WORKDIR /var/www/html

# Copy all project files into the container
COPY . .

# Give Apache proper permissions and create writable SQLite database file
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html \
    && touch /var/www/html/database.sqlite \
    && chmod 666 /var/www/html/database.sqlite \
    && chown www-data:www-data /var/www/html/database.sqlite

# Expose port 80 (Apache default)
EXPOSE 80

# Start Apache in the foreground
CMD ["apache2-foreground"]
