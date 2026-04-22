#!/bin/bash
set -e

a2dismod mpm_event mpm_worker 2>/dev/null || true
a2enmod mpm_prefork 2>/dev/null || true

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

rm -f /var/www/html/public/storage
ln -s /var/www/html/storage/app/public /var/www/html/public/storage

echo "Waiting for database..."
for i in {1..30}; do
    php artisan migrate:status 2>/dev/null && break
    echo "Attempt $i failed, retrying in 3s..."
    sleep 3
done

php artisan migrate --force

php artisan config:cache
php artisan route:cache
php artisan view:cache

exec "$@"