FROM php:8.3-apache

# Instalează dependințe sistem
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    nodejs \
    npm \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Instalează Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Setează working directory
WORKDIR /var/www/html

# Copiază fișierele proiectului
COPY . .

# Creaza directoarele de cache
RUN mkdir -p bootstrap/cache \
    && mkdir -p storage/framework/cache \
    && mkdir -p storage/framework/sessions \
    && mkdir -p storage/framework/views \
    && mkdir -p storage/logs \
    && chmod -R 777 storage bootstrap/cache \

# Instalează dependințe PHP
RUN composer install --no-interaction --optimize-autoloader

# Instalează și build-ui frontend
RUN npm install && npm run build

# Setează permisiuni
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/storage \
    && chmod -R 755 /var/www/html/bootstrap/cache

# Activează mod_rewrite pentru Apache
RUN a2enmod rewrite

# Configurează Apache să folosească public/ ca root
RUN sed -i 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/000-default.conf

# EXPOSE port 80
EXPOSE 80

# Start Apache
CMD ["apache2-foreground"]
