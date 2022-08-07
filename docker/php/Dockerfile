FROM php:8.1.8-fpm-alpine as base
WORKDIR /var/www/html/
COPY --from=mlocati/php-extension-installer:1.5.33 /usr/bin/install-php-extensions /usr/local/bin/
RUN install-php-extensions \
    mysqli \
    opcache \
    pdo_mysql \
    redis \
    zip
COPY --from=composer:2.3.10 /usr/bin/composer /usr/local/bin/composer

FROM base as vendor
COPY composer.json .
COPY composer.lock .
RUN composer check-platform-reqs --quiet \
    && composer i --optimize-autoloader --no-scripts --quiet --no-ansi --no-interaction --no-progress --prefer-dist \
    && mv vendor vendor_test \
    && composer i --optimize-autoloader --no-scripts --quiet --no-dev --no-ansi --no-interaction --no-progress --prefer-dist

FROM node:16.16.0-alpine as node
RUN npm -g install npm@8
WORKDIR /var/www/html
COPY package.json .
COPY package-lock.json .
RUN npm ci
COPY vite.config.js .
COPY postcss.config.js .
COPY tailwind.config.js .
COPY resources ./resources
COPY --from=vendor /var/www/html/vendor /var/www/html/vendor
RUN npm run build

FROM base as application
COPY --chown=www-data:www-data app ./app
COPY --chown=www-data:www-data bootstrap ./bootstrap
COPY --chown=www-data:www-data config ./config
COPY --chown=www-data:www-data database ./database
COPY --chown=www-data:www-data lang ./lang
COPY --chown=www-data:www-data public ./public
COPY --chown=www-data:www-data resources ./resources
COPY --chown=www-data:www-data routes ./routes
COPY --chown=www-data:www-data storage ./storage
COPY --chown=www-data:www-data artisan .
COPY --chown=www-data:www-data composer.json .
COPY --chown=www-data:www-data composer.lock .

FROM application as testing
COPY --chown=www-data:www-data tests ./tests
COPY --chown=www-data:www-data phpunit.xml .
COPY --from=node --chown=www-data:www-data /var/www/html/public /var/www/html/public
COPY --from=vendor /var/www/html/vendor_test /var/www/html/vendor
RUN composer run post-autoload-dump --quiet --no-ansi --no-plugins \
    && php artisan test --parallel

FROM application as production
COPY --from=node --chown=www-data:www-data /var/www/html/public /var/www/html/public
COPY --from=vendor /var/www/html/vendor /var/www/html/vendor
RUN apk add --no-cache fcgiwrap \
    netcat-openbsd \
    procps \
    tini \
    && echo "pm.status_path = /status" >> /usr/local/etc/php-fpm.d/zz-docker.conf \
    && mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"
COPY docker/php/conf.d /usr/local/etc/php/conf.d
ADD https://raw.githubusercontent.com/renatomefi/php-fpm-healthcheck/v0.5.0/php-fpm-healthcheck /usr/local/bin/
ADD https://github.com/eficode/wait-for/releases/download/v2.2.3/wait-for /usr/local/bin/
COPY docker/php/entrypoint.sh /usr/local/bin/entrypoint
RUN chmod u+x /usr/local/bin/php-fpm-healthcheck \
    && chmod u+x /usr/local/bin/wait-for \
    && chmod u+x /usr/local/bin/entrypoint \
    && composer run post-autoload-dump --no-ansi --no-plugins \
    && echo -n "opcache.max_accelerated_files=" >> "$PHP_INI_DIR/conf.d/opcache.ini" \
    && echo $(find . -name "*.php" | wc -l | awk '{print (int($1/1000)+2)*1000}') >> "$PHP_INI_DIR/conf.d/opcache.ini" \
    && rm /usr/local/bin/composer \
    && rm /usr/local/bin/install-php-extensions
ENTRYPOINT ["entrypoint"]
