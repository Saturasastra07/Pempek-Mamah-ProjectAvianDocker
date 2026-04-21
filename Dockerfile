# Menggunakan base image PHP 8.2 dengan Apache
FROM php:8.2-apache

# Install ekstensi yang dibutuhkan Laravel & Database (termasuk pdo_mysql untuk Aiven)
RUN apt-get update && apt-get install -y \
    libzip-dev \
    zip \
    unzip \
    git \
    && docker-php-ext-install pdo_mysql zip

# Aktifkan mod_rewrite Apache (wajib agar routing/link Laravel berjalan)
RUN a2enmod rewrite

# Arahkan web server langsung ke folder /public milik Laravel
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Masukkan Composer untuk menginstal package Laravel
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Atur lokasi kerja di dalam server
WORKDIR /var/www/html

# Pindahkan semua file project kamu ke dalam server Render
COPY . .

# Install dependencies Laravel (abaikan error dev)
RUN composer install --optimize-autoloader --no-dev

# Beri izin akses (permission) agar Laravel bisa menyimpan foto/file/log
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
EXPOSE 80