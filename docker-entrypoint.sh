#!/bin/bash
set -e

# Generate key if not set
if [ -z "$APP_KEY" ] || [ "$APP_KEY" = "" ]; then
    php artisan key:generate --force
fi

# Run migrations
php artisan migrate --force || true
php artisan db:seed --force || true

# Cache
php artisan config:cache
php artisan route:cache
php artisan view:cache

exec "$@"
