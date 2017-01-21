<?php

/*
|--------------------------------------------------------------------------
| Web hook Routes
|--------------------------------------------------------------------------
|
*/


Route::match(['get', 'post'], '/payment/status', 'Payment\ConfirmOrderController@status')->name('payment-status-hook');
