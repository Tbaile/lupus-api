FROM nginx:stable-alpine
WORKDIR /var/www/html
ADD https://raw.githubusercontent.com/eficode/wait-for/v2.1.3/wait-for /usr/local/bin/
COPY docker/nginx/start.sh /usr/local/bin/start
RUN chmod u+x /usr/local/bin/wait-for \
    && chmod u+x /usr/local/bin/start
COPY docker/nginx/config /etc/nginx
COPY . .
ENTRYPOINT ["start"]
CMD ["nginx", "-g", "daemon off;"]
