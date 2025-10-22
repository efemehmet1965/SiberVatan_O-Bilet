FROM php:8.2-apache

ENV DEBIAN_FRONTEND=noninteractive

# Update apt-get repositories
RUN apt-get update

# Install system dependencies
RUN apt-get install -y \
    libsqlite3-dev \
    libzip-dev \
    libpng-dev \
    libjpeg-dev \
    unzip \
    git \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install pdo_sqlite zip gd

# Enable Apache rewrite module
RUN a2enmod rewrite

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy application code
COPY . /var/www/html/

# Set working directory
WORKDIR /var/www/html/

# Set appropriate permissions for the web server
RUN chown -R www-data:www-data /var/www/html
RUN chmod -R 755 /var/www/html

# Enable PHP error reporting for development
COPY .docker/php/php.ini /usr/local/etc/php/conf.d/custom.ini

# Configure Apache to use public directory as DocumentRoot
COPY .docker/apache/000-default.conf /etc/apache2/sites-available/000-default.conf

# Expose port 80
EXPOSE 80

# Entrypoint script to install composer dependencies and setup database
COPY docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN chmod +x /usr/local/bin/docker-entrypoint.sh
ENTRYPOINT ["docker-entrypoint.sh"]
CMD ["apache2-foreground"]
