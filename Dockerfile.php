#
# Install PHP dependencies
#

FROM bethink/composer:1.6.5 AS php-build

ADD . /src
WORKDIR /src

RUN composer install --no-scripts --no-dev

#
# Install JS dependencies & run webpack build
#

FROM node:8.11.3-alpine AS js-build
WORKDIR /src

COPY --from=php-build /src/. .
RUN yarn run setup \
  && node node_modules/cross-env/dist/bin/cross-env.js NODE_ENV=production node_modules/webpack/bin/webpack.js --progress --hide-modules --config=node_modules/laravel-mix/setup/webpack.config.js \
  && rm -rf node_modules && rm -rf resources/assets && rm -f storage/logs/*.log && rm -rf storage/app/public

#
# Build final image
#

FROM php:7.2.7-fpm-alpine3.7

# Install PHP extensions
RUN apk --no-cache add freetype-dev libjpeg-turbo-dev libpng-dev
RUN docker-php-ext-configure gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/ \
	&& docker-php-ext-install -j$(nproc) opcache bcmath gd zip mysqli pdo_mysql

# Install New Relic Agent
RUN curl -L https://download.newrelic.com/php_agent/release/newrelic-php5-8.5.0.235-linux-musl.tar.gz | tar -C /tmp -zx && \
NR_INSTALL_USE_CP_NOT_LN=1 NR_INSTALL_SILENT=1 /tmp/newrelic-php5-*/newrelic-install install && \
rm -rf /tmp/newrelic-php5-* /tmp/nrinstall*

WORKDIR /www/current

COPY --from=js-build /src/. .
RUN touch storage/logs/laravel.log
RUN chown -R 82:82 /www /run /tmp

USER 82
