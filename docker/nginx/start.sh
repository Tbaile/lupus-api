#!/usr/bin/env sh
set -e

if [ "$1" = "nginx" ]; then
    echo "Wait for PHP backend to come up..."
    wait-for -t 30 "app:9000"
fi
echo "Starting $1."
exec /docker-entrypoint.sh "$@"
