#!/bin/sh
set -e

# 1. Wait for Postgres
until nc -z $DB_HOST $DB_PORT; do
  echo "Waiting for database ($DB_HOST)..."
  sleep 2
done

# 2. Only run migrations/optimizations on the "web" node
if [ "$RUN_MIGRATIONS" = "true" ]; then
    echo "Running migrations..."
    php artisan migrate --force

    echo "Linking storage..."
    php artisan storage:link --force

    echo "Publishing assets..."
    php artisan filament:assets
    php artisan livewire:publish --assets
    php artisan horizon:publish

    echo "Caching configuration..."
    php artisan optimize:clear
    php artisan optimize
    php artisan filament:optimize
else
    echo "Skipping migrations (RUN_MIGRATIONS is not set to true)..."
fi

# This allows the Dockerfile CMD or Compose 'command' to run after the script finishes
exec "$@"
