#!/bin/bash
set -e

# Pastikan hanya prefork yang aktif
a2dismod mpm_event mpm_worker 2>/dev/null || true
a2enmod mpm_prefork 2>/dev/null || true

# Generate app key jika belum ada
if [ -z "$APP_KEY" ]; then
    php artisan key:generate --force
fi

# Jalankan migrasi otomatis
php artisan migrate --force

# Jalankan perintah asli (apache2-foreground)
exec "$@"