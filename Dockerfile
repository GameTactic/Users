## Install Deps
FROM composer:latest AS builder
ENV COMPOSER_ALLOW_SUPERUSER 1
ENV APP_ENV prod
ENV APP_SECRET AD1B94FCAB68E57F936A192AA1CFE6E4B7ADC644132EA9D08CC9D7232A70E006
WORKDIR /app

COPY composer.json /app
COPY composer.lock /app
COPY symfony.lock /app
COPY bin /app/bin
COPY config /app/config
COPY src /app/src
COPY public /app/public

RUN composer install \
	--no-ansi \
	--no-dev \
	--no-interaction \
	--no-progress \
	--no-scripts \
	--ignore-platform-reqs \
	--optimize-autoloader \
	--prefer-dist

COPY etc/artifact/.env.prod /app/.env
RUN composer dump-env prod && rm -f .env composer.json composer.lock symfony.lock

## Build Backend
FROM php:7.4-fpm-alpine AS backend
ENV APP_ENV prod

WORKDIR /app

RUN apk add --no-cache libsodium-dev \
    && docker-php-ext-install -j8 sodium \
    && apk del --purge libsodium-dev
RUN docker-php-ext-install -j8 pdo_mysql
RUN apk add nginx supervisor py-pip sudo --no-cache
RUN pip --no-cache-dir install supervisor-stdout
RUN sed -i 's|error_log = /proc/self/fd/2|error_log = /var/log/php-error.log|g' /usr/local/etc/php-fpm.d/docker.conf
RUN touch /var/log/php-error.log;
RUN echo -e "[PHP]\nupload_max_filesize = 2M\npost_max_size = 4M\n" > /usr/local/etc/php/php.ini

COPY --from=builder /app /app
RUN chown www-data:www-data -R /app
RUN sudo -E -u www-data bin/console assets:install --no-ansi -n public

RUN curl -oSs /etc/nginx/conf.d/default.conf https://raw.githubusercontent.com/GameTactic/Deployment/master/artifact/nginx.conf
RUN curl -oSs /etc/supervisord.conf https://raw.githubusercontent.com/GameTactic/Deployment/master/artifact/supervisord.conf
ENTRYPOINT ["/usr/bin/supervisord", "--nodaemon", "--configuration", "/etc/supervisord.conf"]
EXPOSE 80/tcp
