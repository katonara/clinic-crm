#!/bin/bash
set -e

# Substitute PORT in nginx config (Railway sets PORT dynamically)
PORT=${PORT:-8080}
sed -i "s/LISTEN_PORT/$PORT/g" /etc/nginx/nginx.conf

# Generate key if not set
if [ -z "$APP_KEY" ] || [ "$APP_KEY" = "" ]; then
    php artisan key:generate --force
fi

# Run migrations
php artisan migrate --force || true
php artisan db:seed --force || true

# Cache
php artisan config:cache || true
php artisan route:cache || true
php artisan view:cache || true

exec "$@"
