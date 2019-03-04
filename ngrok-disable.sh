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

docker stop ngrok

if [[ "$RELOAD" == "1" ]]
then
    docker-compose up -d php
fi
