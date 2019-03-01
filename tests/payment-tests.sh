#!/usr/bin/env bash

DIR=$(dirname $0)
cd ${DIR}/..

printf "
APP_URL=http://nginx
SESSION_DOMAIN=nginx
" > .env.selenium

./ngrok-enable.sh
docker exec -it php /bin/sh -c 'php artisan dusk tests/Browser/Tests/Payment/PaymentTest.php'
./ngrok-disable.sh

# remove file contents
# we can't remove the file because docker-compose will complain
# see https://github.com/docker/compose/pull/3955
> ./.env.selenium
