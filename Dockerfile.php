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
RUN yarn run setup
RUN node node_modules/cross-env/dist/bin/cross-env.js NODE_ENV=production node_modules/webpack/bin/webpack.js --progress --hide-modules --config=webpack.config.js
RUN rm -rf node_modules && rm -rf resources/assets && rm -f storage/logs/*.log && rm -rf storage/app/public

#
# Build final image
#

FROM bethink/php:7.2.5-fpm-alpine3.7

WORKDIR /www/current

COPY --from=js-build /src/. .
USER root
RUN chown -R 82:82 /www