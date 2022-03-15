#!/usr/bin/env sh
set -e

echo "Waiting for database to come up..."
wait-for -t 30 "${DB_HOST}:${DB_PORT}"
echo "Checking if redis is ready..."
wait-for -t 10 "${REDIS_HOST}:${REDIS_PORT}"
echo "Starting queue daemon"
exec php artisan queue:work --verbose --tries=3 --timeout=90
