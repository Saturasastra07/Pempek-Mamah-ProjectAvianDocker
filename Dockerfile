# Menggunakan base image PHP 8.2 dengan Apache
FROM php:8.2-apache

# Nonaktifkan SEMUA MPM dulu, lalu aktifkan hanya prefork
RUN a2dismod mpm_event mpm_worker mpm_prefork 2>/dev/null || true \
    && a2enmod mpm_prefork

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
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
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

# Tambahkan di akhir Dockerfile sebelum EXPOSE
COPY docker-entrypoint.sh /usr/local/bin/
RUN chmod +x /usr/local/bin/docker-entrypoint.sh
ENTRYPOINT ["docker-entrypoint.sh"]
CMD ["apache2-foreground"]

EXPOSE 80