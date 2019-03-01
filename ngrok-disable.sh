#!/usr/bin/env bash

docker stop ngrok

# remove file contents
# we can't remove the file because docker-compose will complain
# see https://github.com/docker/compose/pull/3955
> ./.env.ngrok

docker-compose up -d
