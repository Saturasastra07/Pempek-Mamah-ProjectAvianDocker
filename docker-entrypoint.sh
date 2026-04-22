#!/bin/bash
set -e

a2dismod mpm_event mpm_worker 2>/dev/null || true
a2enmod mpm_prefork 2>/dev/null || true

# Tulis .env pakai echo satu per satu, lebih aman
echo "APP_NAME=${APP_NAME:-Laravel}" > /var/www/html/.env
echo "APP_ENV=${APP_ENV:-production}" >> /var/www/html/.env
echo "APP_KEY=${APP_KEY:-}" >> /var/www/html/.env
echo "APP_DEBUG=${APP_DEBUG:-false}" >> /var/www/html/.env
echo "APP_URL=${APP_URL:-http://localhost}" >> /var/www/html/.env
echo "" >> /var/www/html/.env
echo "DB_CONNECTION=${DB_CONNECTION:-mysql}" >> /var/www/html/.env
echo "DB_HOST=${DB_HOST:-127.0.0.1}" >> /var/www/html/.env
echo "DB_PORT=${DB_PORT:-3306}" >> /var/www/html/.env
echo "DB_DATABASE=${DB_DATABASE:-laravel}" >> /var/www/html/.env
echo "DB_USERNAME=${DB_USERNAME:-root}" >> /var/www/html/.env
echo "DB_PASSWORD=${DB_PASSWORD:-}" >> /var/www/html/.env
echo "DB_SSLMODE=${DB_SSLMODE:-required}" >> /var/www/html/.env
echo "" >> /var/www/html/.env
echo "CACHE_STORE=${CACHE_STORE:-database}" >> /var/www/html/.env
echo "FILESYSTEM_DISK=${FILESYSTEM_DISK:-public}" >> /var/www/html/.env

php artisan key:generate --force
php artisan config:clear
php artisan cache:clear

# Storage link
rm -f /var/www/html/public/storage
ln -s /var/www/html/storage/app/public /var/www/html/public/storage

# Migrate
php artisan migrate --force

# Cache
php artisan config:cache
php artisan route:cache
php artisan view:cache

exec "$@"