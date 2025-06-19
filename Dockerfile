FROM php:8.2-apache

RUN apt-get update \
    && apt-get install -y default-mysql-client \
    && docker-php-ext-install pdo_mysql \
    && docker-php-ext-enable pdo_mysql

COPY ./app /var/www/html
