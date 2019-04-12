#!/usr/bin/env bash

DIR=$(dirname $0)
cd ${DIR}/..

./ngrok-disable.sh -r 0
docker-compose -f docker-compose.dusk.yml down
