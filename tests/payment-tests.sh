#!/usr/bin/env bash

DIR=$(dirname $0)
cd ${DIR}/..

NGROK_URL=$(./ngrok-enable.sh -r 0)

# ngrok failed
if [[ $? -eq 1 ]]; then
    exit 1
fi

P24_STATUS_URL="$NGROK_URL/payment/status" APP_URL="http://nginx" SESSION_DOMAIN="nginx" DEBUG_BAR="false" docker-compose up -d php

printf "=======================================================\n"
printf "To see what Selenium is doing open vnc://127.0.0.1:5900\n"
printf "              The password is \`secret\`               \n"
printf "              Don't use \`yarn run hot\`               \n"
printf "=======================================================\n"

docker exec -it php /bin/sh -c 'php artisan dusk tests/Browser/Tests/Payment/PaymentTest.php'

./ngrok-disable.sh -r 0

### reset env variables
docker-compose up -d php
