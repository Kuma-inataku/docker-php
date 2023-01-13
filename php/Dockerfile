FROM php:8.2.1-apache

RUN apt-get update && apt-get install -y libonig-dev && \
    docker-php-ext-install pdo_mysql mysqli mbstring

# xdebug install
RUN pecl install xdebug \
  && docker-php-ext-enable xdebug

RUN echo "pulled!!!"