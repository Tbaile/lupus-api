FROM php:8.0.17-fpm-alpine as base
WORKDIR /var/www/html/
COPY --from=mlocati/php-extension-installer:1.5.11 /usr/bin/install-php-extensions /usr/local/bin/
RUN install-php-extensions \
    mysqli \
    opcache \
    pdo_mysql \
    redis \
    zip
COPY --from=composer:2.3.5 /usr/bin/composer /usr/local/bin/composer

FROM base as vendor
COPY composer.json .
COPY composer.lock .
RUN composer check-platform-reqs --quiet \
    && composer i --optimize-autoloader --no-scripts --quiet --no-ansi --no-interaction --no-progress --prefer-dist \
    && mv vendor vendor_test \
    && composer i --optimize-autoloader --no-scripts --quiet --no-dev --no-ansi --no-interaction --no-progress --prefer-dist

FROM node:16.14.2-alpine as node
RUN npm -g install npm@8
WORKDIR /var/www/html
COPY package.json .
COPY package-lock.json .
RUN npm ci
COPY webpack.mix.js .
COPY tailwind.config.js .
COPY resources ./resources
COPY --from=vendor /var/www/html/vendor /var/www/html/vendor
RUN npx mix --production

FROM base as testing
COPY --chown=www-data:www-data . .
COPY --from=node --chown=www-data:www-data /var/www/html/public /var/www/html/public
COPY --from=vendor /var/www/html/vendor_test /var/www/html/vendor
RUN composer run post-autoload-dump --quiet --no-ansi --no-plugins \
    && php artisan test --parallel

FROM base as production
RUN apk add --no-cache fcgiwrap \
    netcat-openbsd \
    procps \
    tini \
    && echo "pm.status_path = /status" >> /usr/local/etc/php-fpm.d/zz-docker.conf \
    && mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"
COPY docker/php/conf.d /usr/local/etc/php/conf.d
ADD https://raw.githubusercontent.com/renatomefi/php-fpm-healthcheck/v0.5.0/php-fpm-healthcheck /usr/local/bin/
ADD https://github.com/eficode/wait-for/releases/download/v2.2.3/wait-for /usr/local/bin/
COPY docker/php/init.sh /usr/local/bin/init
COPY docker/php/scheduler.sh /usr/local/bin/scheduler
COPY docker/php/queue.sh /usr/local/bin/queue
RUN chmod u+x /usr/local/bin/php-fpm-healthcheck \
    && chmod u+x /usr/local/bin/wait-for \
    && chmod u+x /usr/local/bin/init \
    && chmod u+x /usr/local/bin/scheduler \
    && chmod u+x /usr/local/bin/queue
COPY --chown=www-data:www-data . .
COPY --from=testing /var/www/html/artisan /var/www/html/artisan
COPY --from=node --chown=www-data:www-data /var/www/html/public /var/www/html/public
COPY --from=vendor /var/www/html/vendor /var/www/html/vendor
RUN composer run post-autoload-dump --quiet --no-ansi --no-plugins \
    && echo -n "opcache.max_accelerated_files=" >> "$PHP_INI_DIR/conf.d/opcache.ini" \
    && echo $(find . -name "*.php" | wc -l | awk '{print (int($1/1000)+2)*1000}') >> "$PHP_INI_DIR/conf.d/opcache.ini" \
    && rm /usr/local/bin/composer \
    && rm /usr/local/bin/install-php-extensions
ENTRYPOINT ["tini", "--"]
CMD ["init"]
