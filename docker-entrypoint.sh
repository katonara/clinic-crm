#!/bin/bash
set -e

# Substitute PORT in nginx config
PORT=${PORT:-8080}
sed -i "s/LISTEN_PORT/$PORT/g" /etc/nginx/nginx.conf

# Generate key if not set
if [ -z "$APP_KEY" ] || [ "$APP_KEY" = "" ]; then
    php artisan key:generate --force
fi

# Start PHP-FPM in background first (so healthcheck passes)
php-fpm -D

# Run migrations while server is starting
php artisan migrate --force || true

# Cache (fast operations)
php artisan config:cache || true
php artisan route:cache || true
php artisan view:cache || true

# Start Nginx in foreground
exec nginx -g 'daemon off;'
