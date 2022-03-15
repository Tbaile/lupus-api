#!/usr/bin/env sh
set -e

echo "Waiting for database to come up..."
wait-for -t 30 "${DB_HOST}:${DB_PORT}"
echo "Checking if redis is ready..."
wait-for -t 10 "${REDIS_HOST}:${REDIS_PORT}"
echo "Starting scheduler deamon"
exec php artisan schedule:work
