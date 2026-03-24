# --- Stage 1: The Builder (Prep Station) ---
FROM php:8.4-fpm-alpine AS builder

# Install system dependencies for PHP extensions & Node
RUN apk add --no-cache \
    nodejs npm \
    icu-dev \
    libzip-dev \
    libpng-dev \
    postgresql-dev \
    zlib-dev \
    linux-headers

# Install PHP extensions required by Composer
RUN docker-php-ext-install intl zip pcntl pdo_pgsql bcmath gd sockets

WORKDIR /app
COPY . .

# Install Composer dependencies
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Build assets (Vite)
RUN npm install && npm run build

# --- Stage 2: Final Production Image (The Plate) ---
FROM dunglas/frankenphp:1.4-php8.4

# 1. Install Runtime Extensions
RUN apt-get update && apt-get install -y --no-install-recommends \
    supervisor \
    netcat-openbsd \
    && rm -rf /var/lib/apt/lists/*

RUN install-php-extensions \
    pdo_pgsql \
    intl \
    bcmath \
    gd \
    zip \
    pcntl \
    posix \
    exif \
    opcache \
    redis \
    soap \
    sockets

WORKDIR /app

# 2. Copy code from builder
COPY --from=builder /app /app

# 3. Apply your custom PHP config
COPY ./docker/php/local.ini /usr/local/etc/php/conf.d/app.ini

# 4. Setup your Entrypoint
COPY ./docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

# Copy Supervisor Configuration
COPY ./docker/supervisord.conf /etc/supervisord.conf

# 5. Fix Permissions & Cleanup
RUN rm -f /app/frankenphp && \
    ln -s /usr/local/bin/frankenphp /app/frankenphp && \
    chown www-data:www-data /usr/local/bin/frankenphp && \
    chmod +x /usr/local/bin/frankenphp && \
    chown -R www-data:www-data /app/storage /app/bootstrap/cache

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]

# Start Supervisor (default CMD)
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisord.conf"]
