#!/usr/bin/with-contenv sh
wait-for localhost:9000
nginx -g "daemon off;"
