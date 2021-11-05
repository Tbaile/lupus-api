#!/usr/bin/env bash
set -e

echo "Waiting for app container to come up..."
wait-for -t 30 app:9000
echo "Starting queue daemon."
exec php artisan queue:work --verbose --tries=3 --timeout=90
