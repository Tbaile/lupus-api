#!/usr/bin/env sh
set -e

export APP_DOMAIN=${APP_DOMAIN:-localhost}
export FPM_HOST=${FPM_HOST:-127.0.0.1}
export FPM_PORT=${FPM_PORT:-9000}
exec /docker-entrypoint.sh "$@"
