<?php

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

use Barryvdh\Cors\HandleCors;

Route::group(['namespace' => 'Api\PublicApi'], function () {
	$r = config('api.resources');

	Route::group([
		'middleware' => [
			'throttle:200,1',
			HandleCors::class,
		]
	], function () use ($r) {
		// Products
		Route::get("{$r['products']}/current", 'ProductsApiController@getCurrent');
	});

	// Coupons
	Route::post("{$r['coupons']}", 'CouponsApiController@post');
	Route::put("{$r['coupons']}", 'CouponsApiController@put');
	Route::delete("{$r['coupons']}", 'CouponsApiController@deleteCoupon');
});
