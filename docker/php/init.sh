#!/usr/bin/env bash
set -e

echo "Setting up Laravel Framework"
php artisan config:cache
php artisan view:cache
php artisan storage:link
echo "Wait for database to come up..."
wait-for -t 30 "${DB_HOST}:${DB_PORT}"
if [ "${APP_ENV:-production}" == "production" ]; then
    echo "Migrating database..."
    php artisan migrate --force --seed
else
    echo "Application in development mode, resetting database..."
    php artisan migrate:fresh --force --seed
fi

echo "Starting up php-fpm."
exec php-fpm
