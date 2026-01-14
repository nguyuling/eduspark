# Build stage for Node assets
FROM node:18-alpine AS node-builder
WORKDIR /app
COPY package*.json ./
RUN npm ci
COPY . .
RUN npm run build

# Build stage for Composer dependencies
FROM composer:2 AS composer-builder
WORKDIR /app
COPY composer*.json ./
RUN composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist
COPY . .
RUN composer dump-autoload --optimize

# Final production image
FROM php:8.2-fpm-alpine

# Install only essential runtime dependencies in one layer
RUN apk add --no-cache \
    nginx \
    supervisor \
    sqlite \
    libpng \
    && docker-php-ext-install pdo_sqlite opcache \
    && rm -rf /tmp/* /var/cache/apk/*

WORKDIR /var/www

# Copy only built assets and dependencies from build stages
COPY --from=node-builder /app/public/build ./public/build
COPY --from=composer-builder /app/vendor ./vendor

# Copy application code
COPY . .

# Single layer for all directory setup and permissions
RUN mkdir -p storage/framework/{sessions,views,cache} \
    storage/logs bootstrap/cache database \
    && touch database/database.sqlite \
    && chown -R www-data:www-data storage bootstrap/cache database \
    && chmod -R 775 storage bootstrap/cache \
    && chmod 664 database/database.sqlite

# Copy configurations
COPY docker/nginx.conf /etc/nginx/http.d/default.conf
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY docker-entrypoint.sh /usr/local/bin/
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

EXPOSE 8080

ENTRYPOINT ["docker-entrypoint.sh"]
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
