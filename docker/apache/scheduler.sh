#!/usr/bin/env sh
set -e

echo "Waiting for app container to come up..."
wait-for -t 30 app:80
echo "Starting scheduler deamon."
exec php artisan schedule:work
