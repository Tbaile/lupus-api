FROM nginx:1.23.0-alpine
WORKDIR /var/www/html
ADD https://github.com/eficode/wait-for/releases/download/v2.2.3/wait-for /usr/local/bin/
COPY docker/nginx/start.sh /usr/local/bin/start
RUN chmod u+x /usr/local/bin/wait-for \
    && chmod u+x /usr/local/bin/start
COPY docker/nginx/config /etc/nginx
COPY . .
ENTRYPOINT ["start"]
CMD ["nginx", "-g", "daemon off;"]
