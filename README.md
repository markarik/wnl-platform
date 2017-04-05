# Więcej niż LEK - E-learning platform
A platform delivering medical e-learning courses.

## Where to find Models?
app/Models

## Who're the champions?
We are the champtions!

## Artisan commands aka maintenance scripts
### Orders
* to list all orders - `php artisan orders` 
* or show a specific one - `php artisan orders {order ID}`
* or even a few specific ones `php artisan orders {space separated list of orders IDs}`
* cancel an order - `php artisan order:cancel {order ID}`
* mark an order as paid - `php artisan order:paid {order ID}`
* change payment method - `php artisan order:method {order ID} {method}`