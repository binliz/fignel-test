FROM php:8.2-fpm-alpine

ARG UID
ARG GID
ARG USER

ENV COMPOSER_ALLOW_SUPERUSER 1
ENV UID=${UID}
ENV GID=${GID}
ENV USER=${USER}

RUN delgroup dialout

RUN addgroup -g ${GID} --system ${USER}
RUN adduser -G ${USER} --system -D -s /bin/sh -u ${UID} ${USER}

RUN apk add --no-cache --virtual .build-deps $PHPIZE_DEPS && apk add --update linux-headers && pecl install xdebug && docker-php-ext-enable xdebug
COPY ./php/conf.d/xdebug.ini /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

RUN cp $PHP_INI_DIR/php.ini-production $PHP_INI_DIR/php.ini

RUN sed -i "s/user = www-data/user = '${USER}'/g" /usr/local/etc/php-fpm.d/www.conf
RUN sed -i "s/group = www-data/group = '${USER}'/g" /usr/local/etc/php-fpm.d/www.conf

WORKDIR /app
COPY ./src /app

USER ${USER}

