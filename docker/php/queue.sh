#!/usr/bin/env sh
set -e

echo "Waiting for database to come up..."
wait-for -t 30 "${DB_HOST}:${DB_PORT}"
echo "Starting queue daemon."
exec php artisan queue:work --verbose --tries=3 --timeout=90
