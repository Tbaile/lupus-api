FROM nginx:stable as base
WORKDIR /var/www/html
HEALTHCHECK --interval=2s --start-period=3s CMD curl -f http://localhost/ || exit 1
RUN apt-get update -qq \
    && apt-get install -yqq netcat \
    && rm -rf /var/lib/apt/lists/*
ADD https://raw.githubusercontent.com/eficode/wait-for/v2.1.3/wait-for /usr/local/bin/
COPY docker/nginx/start.sh /usr/local/bin/start
RUN chmod u+x /usr/local/bin/wait-for \
    && chmod u+x /usr/local/bin/start
COPY docker/nginx/config /etc/nginx
ENTRYPOINT ["start"]
CMD ["nginx", "-g", "daemon off;"]

FROM base as production
COPY --chown=www-data:www-data . .

