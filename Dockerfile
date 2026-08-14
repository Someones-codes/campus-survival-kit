# Stage 1: Build frontend assets with Node
FROM node:20-alpine AS node-builder

WORKDIR /app

COPY package*.json ./
RUN npm install

COPY . .
RUN npm run build

# Stage 2: PHP application with Nginx + PHP-FPM
FROM php:8.2-fpm AS app

RUN apt-get update && apt-get install -y \
    git \
    curl \
    nginx \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libpq-dev \
    libzip-dev \
    zip \
    unzip \
    && docker-php-ext-install pdo_pgsql pgsql mbstring exif pcntl bcmath gd zip \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist

COPY . .
COPY --from=node-builder /app/public/build ./public/build

RUN composer dump-autoload --optimize

RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Nginx config: serve Laravel's public/ folder, proxy PHP to php-fpm
RUN echo 'server { \
    listen 10000; \
    server_name _; \
    root /var/www/html/public; \
    index index.php; \
    client_max_body_size 20M; \
    location / { \
        try_files $uri $uri/ /index.php?$query_string; \
    } \
    location ~ \.php$ { \
        fastcgi_pass 127.0.0.1:9000; \
        fastcgi_index index.php; \
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name; \
        include fastcgi_params; \
        fastcgi_read_timeout 60; \
    } \
    location ~ /\.ht { \
        deny all; \
    } \
}' > /etc/nginx/sites-available/default

EXPOSE 10000

# Startup script: run Laravel caching/migrations once, then start php-fpm + nginx together
RUN echo '#!/bin/sh \n\
php artisan config:cache \n\
php artisan route:cache \n\
php artisan view:cache \n\
php artisan migrate --force \n\
php-fpm -D \n\
nginx -g "daemon off;" \n\
' > /start.sh && chmod +x /start.sh

CMD ["/start.sh"]