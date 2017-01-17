<?php

/*
|--------------------------------------------------------------------------
| Web hook Routes
|--------------------------------------------------------------------------
|
*/


Route::match(['get', 'post'], '/payment/status', 'Payment\StepThreeController@status');
