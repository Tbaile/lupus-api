#!/usr/bin/with-contenv sh
mkdir /etc/nginx/conf.d
envsubst '${APP_DOMAIN}' < /etc/nginx/templates/default.conf.template > /etc/nginx/conf.d/default.conf
