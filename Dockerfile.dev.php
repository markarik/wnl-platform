FROM php:7.3.2-fpm-alpine3.9

USER root

# Install PHP extensions
RUN apk --no-cache add freetype-dev libjpeg-turbo-dev libpng-dev libzip-dev
RUN docker-php-ext-configure gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/ \
	&& docker-php-ext-install -j$(nproc) opcache bcmath gd zip mysqli pdo_mysql

