#!/usr/bin/env bash
set -e

echo "Wait for PHP backend to come up..."
wait-for -t 30 "app:9000"
echo "Starting $1."
exec /docker-entrypoint.sh "$@"
