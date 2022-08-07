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
        echo "Waiting for database to come up..."
        wait-for -t 30 "${DB_HOST}:${DB_PORT}"
        echo "Checking if redis is ready..."
        wait-for -t 10 "${REDIS_HOST}:${REDIS_PORT}"
        echo "Setting up Laravel Framework"
        php artisan config:cache
        php artisan view:cache
        php artisan storage:link
        echo "Copying the public folder data to the shared volume"
        cp -r public /app
        if [ "${APP_ENV:-production}" = "production" ]; then
            echo "Migrating database"
            php artisan migrate --force --seed
        else
            echo "Application in development mode, resetting database"
            php artisan migrate:fresh --force --seed
        fi
    else
        echo "Unknown role '$ROLE'"
        exit 1
    fi
fi
