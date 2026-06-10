#!/bin/bash
set -e

# Use Railway's PORT or default to 80
if [ -n "$PORT" ]; then
    sed -i "s/Listen 80/Listen $PORT/" /etc/apache2/ports.conf
    sed -i "s/:80/:$PORT/" /etc/apache2/sites-available/*.conf
fi

# Generate key if not set
if [ -z "$APP_KEY" ] || [ "$APP_KEY" = "" ]; then
    php artisan key:generate --force
fi

# Run migrations (seed only on fresh)
php artisan migrate --force || true
php artisan db:seed --force || true

# Cache after migrations
php artisan config:cache
php artisan route:cache
php artisan view:cache

exec "$@"
