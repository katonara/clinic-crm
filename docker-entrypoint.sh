#!/bin/bash

# Substitute PORT in nginx config
PORT=${PORT:-8080}
sed -i "s/LISTEN_PORT/$PORT/g" /etc/nginx/nginx.conf

# Start PHP-FPM
php-fpm -D

# Start Nginx
exec nginx -g 'daemon off;'
