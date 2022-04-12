#!/usr/bin/env sh
set -e

echo "Setting up Laravel Framework"
php artisan config:cache
php artisan view:cache
php artisan storage:link
echo "Clone the public folder data to the mounted volume"
cp -r public /app
echo "Wait for database to come up..."
wait-for -t 30 "${DB_HOST}:${DB_PORT}"
if [ "${APP_ENV:-production}" = "production" ]; then
    echo "Migrating database"
    php artisan migrate --force --seed
else
    echo "Application in development mode, resetting database"
    php artisan migrate:fresh --force --seed
fi
echo "Checking if redis is ready..."
wait-for -t 10 "${REDIS_HOST}:${REDIS_PORT}"
echo "Starting up php-fpm"
exec php-fpm
