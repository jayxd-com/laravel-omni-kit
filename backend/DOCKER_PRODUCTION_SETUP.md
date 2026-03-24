# Docker Production Setup

## 1. Dockerfile

```dockerfile
# --- Stage 1: Build Assets (Node 22) ---
FROM node:22-alpine AS frontend-builder
WORKDIR /app
COPY package*.json ./
RUN npm install
COPY . .
RUN npm run build

# --- Stage 2: Final Production Image (PHP 8.4) ---
FROM php:8.4-fpm-alpine

# Install system dependencies
RUN apk add --no-cache \
    libpng-dev libzip-dev zip unzip git \
    postgresql-dev postgresql-libs icu-dev

# Install PHP extensions
RUN docker-php-ext-install pdo_pgsql gd zip intl bcmath pcntl
RUN pecl install redis && docker-php-ext-enable redis

# Production PHP settings
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

WORKDIR /var/www/html
COPY . .

# Copy compiled assets from Stage 1
COPY --from=frontend-builder /app/public/build ./public/build

# Get Composer & Install dependencies
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Permissions for storage and cache
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

EXPOSE 9000
CMD ["php-fpm"]
```

## 2. docker-compose.yml

```yaml
services:
  app:
    build: .
    restart: unless-stopped
    env_file: .env
    volumes:
      - laravel_storage:/var/www/html/storage
    networks:
      - dokploy-network

  web:
    image: nginx:alpine
    restart: unless-stopped
    volumes:
      - .:/var/www/html
      - laravel_storage:/var/www/html/storage
      - ./docker/nginx/default.conf:/etc/nginx/conf.d/default.conf
    depends_on:
      - app
    networks:
      - dokploy-network

  horizon:
    build: .
    restart: unless-stopped
    env_file: .env
    command: php artisan horizon
    stop_signal: SIGTERM
    volumes:
      - laravel_storage:/var/www/html/storage
    networks:
      - dokploy-network

  scheduler:
    build: .
    restart: unless-stopped
    env_file: .env
    command: sh -c "while [ true ]; do php artisan schedule:run --no-interaction & sleep 60; done"
    volumes:
      - laravel_storage:/var/www/html/storage
    networks:
      - dokploy-network

volumes:
  laravel_storage:

networks:
  dokploy-network:
    external: true
    name: dokploy-network
```

## 3. docker/nginx/default.conf

```nginx
server {
    listen 80;
    index index.php index.html;
    root /var/www/html/public;

    location ^~ /livewire/ {
        proxy_pass http://app:9000;
        proxy_set_header Host $http_host;
    }

    location ~ \.php$ {
        try_files $uri =404;
        fastcgi_pass app:9000;
        fastcgi_index index.php;
        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        fastcgi_read_timeout 600;
    }

    location / {
        try_files $uri $uri/ /index.php?$query_string;
        gzip_static on;
    }
}
```

## 4. .dockerignore

```text
.git
.env
.env.*
nod_modules
vendor
storage/logs/*
storage/framework/cache/*
storage/framework/sessions/*
storage/framework/views/*
public/storage
tests
Dockerfile
docker-compose.yml
```