#!/usr/bin/env bash

DIR=$(dirname $0)
cd ${DIR}/..

DUSK_ARGS="tests/Browser/Tests/Payment/PaymentTest.php"

if [[ -n $1 ]]
then
    DUSK_ARGS=$1
fi

docker exec -it php /bin/sh -c "php artisan dusk $DUSK_ARGS"
