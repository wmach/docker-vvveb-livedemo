FROM php:8.2-apache

WORKDIR /var/www/html
RUN apt-get -y update && apt-get install -y \
    libonig-dev \
    curl \
    libzip-dev \
    gnupg \
    locales \
    gettext \
  && docker-php-ext-install \
    pdo_mysql \
    mysqli \
    zip \
    gettext \
  && docker-php-ext-enable \
    gettext

RUN curl -sL https://deb.nodesource.com/setup_16.x | bash -
RUN apt-get install -y npm

# RUN npm install npm@latest -g
RUN npm install -g gulp
RUN npm uninstall node-sass
RUN npm install sass

COPY ./config/php/php.ini /usr/local/etc/php/

