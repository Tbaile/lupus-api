FROM php:8-fpm as base
WORKDIR /var/www/html/
# Copying extension installer then install every dependency needed for the image to work
COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/
RUN set -xe && echo "pm.status_path = /status" >> /usr/local/etc/php-fpm.d/zz-docker.conf \
    && apt-get update -qq \
    && apt-get install -yqq fcgiwrap tini netcat procps \
    && rm -rf /var/lib/apt/lists/* \
    && mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini" \
    && install-php-extensions zip pdo_pgsql pgsql
# Copying needed binaries
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
# Change entrypoint, using tini because some processes doesn't handle well SIGTERM signals
ENTRYPOINT ["tini", "--"]
CMD ["init"]

FROM base as vendor
COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer
COPY --chown=www-data:www-data . .
RUN composer check-platform-reqs --quiet \
    && composer i --optimize-autoloader --quiet --no-dev --no-ansi --no-interaction --no-progress --prefer-dist

FROM node:lts as node
WORKDIR /var/www/html
COPY package.json .
COPY package-lock.json .
RUN npm ci
COPY resources ./resources
COPY webpack.mix.js .
COPY tailwind.config.js .
COPY --from=vendor /var/www/html/vendor /var/www/html/vendor
RUN npx mix --production \
    && rm -rf node_modules

FROM vendor as testing
COPY --from=node --chown=www-data:www-data /var/www/html/public /var/www/html/public
RUN cp -r vendor vendor_prod \
    && composer i --quiet --no-ansi --no-interaction --no-progress --prefer-dist \
    && php artisan test --parallel

FROM testing as production
RUN rm -rf vendor \
    && mv vendor_prod vendor \
    && composer i --optimize-autoloader --quiet --no-dev --no-ansi --no-interaction --no-progress --prefer-dist \
    && rm /usr/local/bin/composer \
    && rm /usr/local/bin/install-php-extensions


