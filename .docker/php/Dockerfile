FROM php:8.2-fpm-alpine

ENV DOCUMENT_ROOT /var/www/
WORKDIR ${DOCUMENT_ROOT}
RUN rm -rf "${DOCUMENT_ROOT}html"

RUN apk add --no-cache pcre-dev icu-dev git htop nano linux-headers bash
RUN apk add --no-cache libzip-dev curl-dev libxml2 libxml2-dev
RUN apk add --no-cache alpine-sdk
RUN apk add --no-cache autoconf build-base libzip-dev zip gnupg
RUN apk add --no-cache su-exec unzip


# Retrieve the script used to install PHP extensions from the source container.
COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/bin/install-php-extensions

RUN rm -rf /var/cache/apk/*

RUN docker-php-ext-install \
    opcache \
    intl \
    pcntl \
    pdo \
    bcmath \
    xml \
    pdo_mysql \
    exif


RUN pecl install xdebug && pecl clear-cache; \
    docker-php-ext-enable xdebug

RUN chmod uga+x /usr/bin/install-php-extensions \
    && sync \
    && install-php-extensions pdo_sqlsrv sqlsrv

RUN echo "memory_limit = -1" > /usr/local/etc/php/conf.d/memory_limit.ini;

ENV COMPOSER_ALLOW_SUPERUSER=1
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/bin --filename=composer
