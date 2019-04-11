#!/usr/bin/env bash

DIR=$(dirname $0)
cd ${DIR}/..

docker-compose -f docker-compose.yaml -f docker-compose.dusk.yml up --remove-orphans -d

NGROK_URL=$(./ngrok-enable.sh -r 0)

# ngrok failed
if [[ $? -eq 1 ]]; then
    exit 1
fi

P24_STATUS_URL="$NGROK_URL/payment/status" docker-compose -f docker-compose.yaml -f docker-compose.dusk.yml up -d php

printf "=======================================================\n"
printf "If you want to run specific test use an argument, e.g.:\n"
printf "         \`payment-tests.sh --filter=studyBuddy\`      \n"
printf "                                                       \n"
printf "To see what Selenium is doing open vnc://127.0.0.1:5900\n"
printf "              The password is \`secret\`               \n"
printf "              Don't use \`yarn run hot\`               \n"
printf "=======================================================\n"

docker exec -it php php artisan migrate
docker exec -it php php artisan db:seed
