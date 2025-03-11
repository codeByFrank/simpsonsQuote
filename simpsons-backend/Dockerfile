# Use an official PHP-Apache image (PHP 8.2)
FROM php:8.2-apache

# Install required packages and PHP extensions
RUN apt-get update && apt-get install -y \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    && docker-php-ext-install pdo pdo_mysql

# Copy Composer from the official Composer image
COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

# Set the working directory
WORKDIR /var/www/html

# Copy the entire Laravel project into the container
COPY . /var/www/html

# Update Apache configuration: set DocumentRoot to /var/www/html/public
RUN sed -ri 's#DocumentRoot /var/www/html#DocumentRoot /var/www/html/public#' /etc/apache2/sites-available/000-default.conf && \
    sed -ri 's#<Directory /var/www/html>#<Directory /var/www/html/public>#' /etc/apache2/sites-available/000-default.conf

# Install PHP dependencies via Composer (without dev packages and with optimized autoloader)
RUN composer install --no-dev --optimize-autoloader

# Set proper file permissions: change owner to www-data and set directories to 755 and files to 644
RUN chown -R www-data:www-data /var/www/html && \
    find /var/www/html -type d -exec chmod 755 {} \; && \
    find /var/www/html -type f -exec chmod 644 {} \;

# Enable Apache's rewrite module
RUN a2enmod rewrite

# Expose port 80
EXPOSE 80

# Start Apache in the foreground
CMD ["apache2-foreground"]
