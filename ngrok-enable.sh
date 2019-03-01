#!/usr/bin/env bash

docker run --rm -d --name=ngrok --network=container:nginx wernight/ngrok ngrok http --region=eu nginx:80

# It takes some time for ngrok to set up public URL
sleep 2

NGROK_URL=$(docker run --rm --name=jq --network=container:nginx endeveit/docker-jq sh -c 'curl -s http://nginx:4040/api/tunnels/command_line | jq -r .public_url')

if [[ "$NGROK_URL" == "null" ]]
then
    printf "ngrok failed to return public url\n"
    exit 1
fi

printf "ngrok URL: $NGROK_URL\n"

printf "
P24_STATUS_URL=$NGROK_URL/payment/status
" > .env.ngrok

docker-compose up -d
