#!/bin/bash

# Copy lesson PDFs from public/storage to storage/app/lessons if they exist
if [ -d "public/storage/lessons" ]; then
    mkdir -p storage/app/lessons
    cp -r public/storage/lessons/* storage/app/lessons/ 2>/dev/null || true
    echo "PDFs copied to storage/app/lessons"
fi

# Ensure storage directories have correct permissions
mkdir -p storage/app/lessons
mkdir -p storage/logs
mkdir -p bootstrap/cache

chown -R www-data:www-data storage bootstrap/cache database
chmod -R 775 storage bootstrap/cache database

# Create symlink for public storage access
php artisan storage:link || true

echo "Storage setup complete"
