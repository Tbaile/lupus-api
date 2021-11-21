FROM php:8-fpm-alpine as base
WORKDIR /var/www/html/
COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/
RUN install-php-extensions zip pdo_pgsql pgsql
COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer

FROM base as vendor
COPY composer.json .
COPY composer.lock .
RUN composer check-platform-reqs --quiet \
  && composer i --optimize-autoloader --no-scripts --quiet --no-ansi --no-interaction --no-progress --prefer-dist \
  && mv vendor vendor_test \
  && composer i --optimize-autoloader --no-scripts --quiet --no-dev --no-ansi --no-interaction --no-progress --prefer-dist

FROM node:lts-alpine as node
WORKDIR /var/www/html
COPY package.json .
COPY package-lock.json .
RUN npm ci
COPY webpack.mix.js .
COPY tailwind.config.js .
COPY resources ./resources
COPY --from=vendor /var/www/html/vendor /var/www/html/vendor
RUN npx mix --production \
    && rm -rf node_modules

FROM base as testing
COPY --chown=www-data:www-data . .
COPY --from=node --chown=www-data:www-data /var/www/html/public /var/www/html/public
COPY --from=vendor /var/www/html/vendor_test /var/www/html/vendor
RUN composer run post-autoload-dump --quiet --no-ansi --no-plugins \
    && php artisan test --parallel

FROM base as production
RUN apk add --no-cache fcgiwrap tini netcat-openbsd procps \
  && echo "pm.status_path = /status" >> /usr/local/etc/php-fpm.d/zz-docker.conf \
  && mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"
ADD https://raw.githubusercontent.com/renatomefi/php-fpm-healthcheck/v0.5.0/php-fpm-healthcheck /usr/local/bin/
ADD https://raw.githubusercontent.com/eficode/wait-for/v2.1.3/wait-for /usr/local/bin/
COPY docker/php/init.sh /usr/local/bin/init
COPY docker/php/scheduler.sh /usr/local/bin/scheduler
COPY docker/php/queue.sh /usr/local/bin/queue
RUN chmod u+x /usr/local/bin/php-fpm-healthcheck \
  && chmod u+x /usr/local/bin/wait-for \
  && chmod u+x /usr/local/bin/init \
  && chmod u+x /usr/local/bin/scheduler \
  && chmod u+x /usr/local/bin/queue
COPY --chown=www-data:www-data . .
COPY --from=node --chown=www-data:www-data /var/www/html/public /var/www/html/public
COPY --from=vendor /var/www/html/vendor_test /var/www/html/vendor
RUN composer run post-autoload-dump --quiet --no-ansi --no-plugins \
  && rm /usr/local/bin/composer \
  && rm /usr/local/bin/install-php-extensions
ENTRYPOINT ["tini", "--"]
CMD ["init"]
