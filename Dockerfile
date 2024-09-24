FROM composer:latest AS builder
COPY bin /usr/src/CollabpadsBackend/bin
COPY src /usr/src/CollabpadsBackend/src
COPY composer.json /usr/src/CollabpadsBackend/
COPY config.docker.php /usr/src/CollabpadsBackend/config.php
RUN cd /usr/src/CollabpadsBackend/ && composer update --ignore-platform-req ext-mongodb

FROM php:8.1-cli
RUN mkdir -p /usr/src/CollabpadsBackend
COPY --from=builder /usr/src/CollabpadsBackend/ /usr/src/CollabpadsBackend/ 
COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/
RUN install-php-extensions mongodb-stable
WORKDIR /usr/src/CollabpadsBackend
ENTRYPOINT [ "php", "./bin/server.php" ]
