#!/usr/bin/env sh
set -e

ROLE=${ROLE:-app}

if [ -n "$1" ]; then
    echo "Executing $1"
    exec "$@"
else
    if [ "$ROLE" = "app" ]; then
        exec php-fpm
    elif [ "$ROLE" = "queue" ]; then
        exec tini php artisan queue:work --verbose --tries=3 --timeout=90
    elif [ "$ROLE" = "scheduler" ]; then
        exec tini php artisan schedule:work
    elif [ "$ROLE" = "setup" ]; then
        exec php artisan app:setup
    else
        echo "Unknown role '$ROLE'"
        exit 1
    fi
fi
