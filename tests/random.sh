#!/bin/bash

for ((i = 1; i <= $1; i++)); 
do
  php artisan dusk --filter=randomCheckoutTest; 
  cat scenario.dusk
done
