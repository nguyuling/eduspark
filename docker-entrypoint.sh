#!/bin/bash
set -e

# Ensure storage directories exist
mkdir -p storage/framework/{sessions,views,cache}
mkdir -p storage/logs
mkdir -p bootstrap/cache

# Set permissions
chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache

# Create database if not exists
if [ ! -f database/database.sqlite ]; then
    touch database/database.sqlite
fi
chown www-data:www-data database/database.sqlite
chmod 664 database/database.sqlite

# Run migrations
php artisan migrate --force || echo "Migration failed, continuing..."

# Cache configuration
php artisan config:cache || echo "Config cache failed, continuing..."
php artisan route:cache || echo "Route cache failed, continuing..."
php artisan view:cache || echo "View cache failed, continuing..."

# Start Apache
exec "$@"
