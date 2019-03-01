#!/usr/bin/env bash

../ngrok-enable.sh
docker exec php /bin/sh -c 'php artisan dusk tests/Browser/Tests/Payment/PaymentTest.php'
