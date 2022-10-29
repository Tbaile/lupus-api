# syntax=docker/dockerfile:1.4.3
FROM nginx:1.23.2-alpine as production
WORKDIR /var/www/html
ADD --link https://github.com/eficode/wait-for/releases/download/v2.2.3/wait-for /usr/local/bin/
COPY --link containers/nginx/start.sh /usr/local/bin/start
RUN chmod u+x /usr/local/bin/wait-for \
    && chmod u+x /usr/local/bin/start
COPY --link containers/nginx/config /etc/nginx
COPY --link public public
ENTRYPOINT ["start"]
CMD ["nginx", "-g", "daemon off;"]
