#
# Install PHP dependencies
#

FROM composer:1.8.4 AS php-build

RUN composer global require hirak/prestissimo

WORKDIR /src
COPY composer.json composer.json
COPY composer.lock composer.lock

RUN composer install --no-scripts --no-autoloader

COPY . ./
RUN composer dump-autoload --no-scripts --optimize

#
# Install JS dependencies & run webpack build
#

FROM node:8.11.3-alpine AS js-build
WORKDIR /src

COPY package.json package.json
COPY yarn.lock yarn.lock
RUN yarn run setup

COPY --from=php-build /src/. ./
RUN node node_modules/cross-env/dist/bin/cross-env.js NODE_ENV=production node_modules/webpack/bin/webpack.js \
	--progress --hide-modules --config=node_modules/laravel-mix/setup/webpack.config.js && rm -rf node_modules \
	&& rm -rf resources/assets && rm -f storage/logs/*.log && rm -rf storage/app/public

#
# Build final image
#

FROM php:7.3.2-fpm-alpine3.9

# Install PHP extensions
RUN apk --no-cache add freetype-dev libjpeg-turbo-dev libpng-dev libzip-dev
RUN docker-php-ext-configure gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/ \
&& docker-php-ext-install -j$(nproc) opcache bcmath gd zip mysqli pdo_mysql

# Install New Relic Agent
RUN curl -L https://download.newrelic.com/php_agent/archive/8.5.0.235/newrelic-php5-8.5.0.235-linux-musl.tar.gz | tar -C /tmp -zx && \
NR_INSTALL_USE_CP_NOT_LN=1 NR_INSTALL_SILENT=1 /tmp/newrelic-php5-*/newrelic-install install && \
rm -rf /tmp/newrelic-php5-* /tmp/nrinstall*

WORKDIR /www/current

COPY --from=js-build /src/. ./
RUN chown -R 82:82 /www /run /tmp

USER 82
