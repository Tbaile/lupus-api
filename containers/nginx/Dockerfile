# syntax=docker/dockerfile:1.4.3
FROM nginx:1.24.0-alpine as production
WORKDIR /var/www/html
ADD https://github.com/eficode/wait-for/releases/download/v2.2.3/wait-for /usr/local/bin/
COPY containers/nginx/start.sh /usr/local/bin/start
RUN chmod u+x /usr/local/bin/wait-for \
    && chmod u+x /usr/local/bin/start
COPY containers/nginx/config /etc/nginx
COPY public public
ENTRYPOINT ["start"]
CMD ["nginx", "-g", "daemon off;"]
