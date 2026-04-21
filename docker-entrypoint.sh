#!/bin/bash
set -e

# Pastikan hanya prefork yang aktif
a2dismod mpm_event mpm_worker 2>/dev/null || true
a2enmod mpm_prefork 2>/dev/null || true

# Buat file .env dari environment variables Railway
cp .env.example .env

# Override nilai dari env Railway
cat > .env << EOF
APP_NAME="${APP_NAME:-Laravel}"
APP_ENV="${APP_ENV:-production}"
APP_KEY="${APP_KEY:-}"
APP_DEBUG="${APP_DEBUG:-false}"
APP_URL="${APP_URL:-http://localhost}"

DB_CONNECTION="${DB_CONNECTION:-mysql}"
DB_HOST="${DB_HOST:-127.0.0.1}"
DB_PORT="${DB_PORT:-3306}"
DB_DATABASE="${DB_DATABASE:-laravel}"
DB_USERNAME="${DB_USERNAME:-root}"
DB_PASSWORD="${DB_PASSWORD:-}"

CACHE_STORE="${CACHE_STORE:-database}"
FILESYSTEM_DISK="${FILESYSTEM_DISK:-public}"
EOF

# Generate app key
php artisan key:generate --force

# Jalankan migrasi
php artisan migrate --force

# Jalankan perintah asli
exec "$@"