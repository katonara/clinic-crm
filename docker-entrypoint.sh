#!/bin/bash

# Substitute PORT in nginx config
PORT=${PORT:-8080}
sed -i "s/LISTEN_PORT/$PORT/g" /etc/nginx/nginx.conf

# Start PHP-FPM first (so healthcheck passes quickly)
php-fpm -D

# Start Nginx in background temporarily
nginx

# Now run migrations while server is already responding
php artisan migrate --force 2>/dev/null || true
php artisan db:seed --force 2>/dev/null || true
php artisan config:cache 2>/dev/null || true
php artisan route:cache 2>/dev/null || true
php artisan view:cache 2>/dev/null || true

# Stop background nginx, restart in foreground
nginx -s stop
exec nginx -g 'daemon off;'
