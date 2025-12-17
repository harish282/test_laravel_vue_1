#!/bin/bash

# Set permissions
chown -R www-data:www-data /var/www/html
chmod -R 755 /var/www/html/storage

# Install dependencies
composer update --no-dev --optimize-autoloader

# Generate key
php artisan key:generate

# Start Reverb in background
php artisan reverb:start &

# Start Laravel server
php artisan serve --host=0.0.0.0 --port=8000