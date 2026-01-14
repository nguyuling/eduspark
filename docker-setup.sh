#!/bin/bash

# Copy lesson PDFs bundled in repo into storage locations used by the app
if [ -d "storage/app/lessons" ]; then
    mkdir -p storage/app/public/lessons
    mkdir -p public/storage/lessons
    cp -r storage/app/lessons/* storage/app/public/lessons/ 2>/dev/null || true
    cp -r storage/app/lessons/* public/storage/lessons/ 2>/dev/null || true
    echo "PDFs staged to storage/app/public/lessons and public/storage/lessons"
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
