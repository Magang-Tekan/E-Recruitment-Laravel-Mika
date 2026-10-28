# ==========================================
# STAGE 1: Frontend Asset Compilation (Vite)
# ==========================================
FROM node:20-alpine AS frontend
WORKDIR /app

# Copy package manifests and install dependencies
COPY package*.json ./
RUN npm ci || npm install

# Copy application assets and build
COPY . .
RUN npm run build

# ==========================================
# STAGE 2: PHP-FPM Application Runtime
# ==========================================
FROM php:8.4-fpm-alpine AS app

ARG APP_REVISION=unknown
LABEL org.opencontainers.image.revision="${APP_REVISION}"

# Install official PHP extension installer helper
COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/

# Install system utilities and PHP extensions
RUN apk update && apk add --no-cache curl git unzip \
    && install-php-extensions \
        pdo_pgsql \
        pdo_mysql \
        mbstring \
        exif \
        pcntl \
        bcmath \
        gd \
        intl \
        zip \
        opcache

# Get latest Composer binary
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Custom PHP settings
COPY docker/php/local.ini /usr/local/etc/php/conf.d/local.ini
COPY docker/php/opcache.ini /usr/local/etc/php/conf.d/opcache.ini

# Set working directory
WORKDIR /var/www

# Copy application code
COPY . .

# Copy compiled frontend assets from Stage 1
COPY --from=frontend /app/public/build /var/www/public/build

# Install Composer dependencies (production optimized)
ENV COMPOSER_ALLOW_SUPERUSER=1
RUN composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist

# Create necessary directories and set correct permissions for www-data
RUN mkdir -p /var/www/storage/framework/cache \
             /var/www/storage/framework/sessions \
             /var/www/storage/framework/views \
             /var/www/storage/app/public \
             /var/www/storage/logs \
             /var/www/bootstrap/cache \
    && chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache /var/www/public \
    && chmod -R 775 /var/www/storage /var/www/bootstrap/cache /var/www/public

USER www-data

EXPOSE 9000
CMD ["php-fpm"]

# ==========================================
# STAGE 3: Nginx Web Server
# ==========================================
FROM nginx:alpine AS web

ARG APP_REVISION=unknown
LABEL org.opencontainers.image.revision="${APP_REVISION}"

COPY docker/nginx/default.conf /etc/nginx/conf.d/default.conf
COPY --from=app /var/www/public /var/www/public

EXPOSE 80
CMD ["nginx", "-g", "daemon off;"]
