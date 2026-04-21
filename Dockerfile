# Menggunakan base image PHP 8.2 dengan Apache
FROM php:8.2-apache

# Matikan mpm_event dan nyalakan mpm_prefork biar gak bentrok
RUN a2dismod mpm_event && a2enmod mpm_prefork

# Install ekstensi yang dibutuhkan Laravel & Database
RUN apt-get update && apt-get install -y \
    libzip-dev \
    zip \
    unzip \
    git \
    && docker-php-ext-install pdo_mysql zip

# Aktifkan mod_rewrite Apache
RUN a2enmod rewrite

# Arahkan web server ke folder /public Laravel
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Masukkan Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Atur lokasi kerja
WORKDIR /var/www/html

# Pindahkan file project
COPY . .

# Install dependencies
RUN composer install --optimize-autoloader --no-dev

# Beri izin akses folder storage
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

EXPOSE 80