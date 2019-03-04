#!/usr/bin/env bash

RELOAD=1

while true ; do
    case "$1" in
        -r )
            RELOAD=$2
            shift 1
        ;;
        *)
            break
        ;;
    esac
done;

# Send container id to /dev/null, so we can use output of this script in payment-tests.sh
docker run --rm -d --name=ngrok --network=container:nginx wernight/ngrok ngrok http --region=eu nginx:80 > /dev/null

# It takes some time for ngrok to set up public URL
sleep 2

NGROK_URL=$(docker run --rm --name=jq --network=container:nginx endeveit/docker-jq sh -c 'curl -s http://nginx:4040/api/tunnels/command_line | jq -r .public_url')

if [[ "$NGROK_URL" == "null" ]]
then
    printf "ngrok failed to return public url\n"
    exit 1
fi


if [[ "$RELOAD" == "1" ]]
then
    printf "ngrok URL: $NGROK_URL\n"
    P24_STATUS_URL="$NGROK_URL/payment/status" docker-compose up -d php
else
    echo ${NGROK_URL}
fi
