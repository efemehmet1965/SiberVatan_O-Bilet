#!/bin/bash

set -e

# Install Composer dependencies
composer install --no-interaction --prefer-dist --optimize-autoloader

# Run database setup and seeding scripts
php database/setup.php
php database/seed.php
php database/seed_admin.php
php database/seed_trip.php

# Set permissions for the database directory and file
chown -R www-data:www-data /var/www/html/database
chmod -R 775 /var/www/html/database

# Execute the main command
exec "$@"
