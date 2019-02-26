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

Route::group(['namespace' => 'Api\PublicApi'], function () {
	$r = config('api.resources');

	// Products
	Route::get('products/availability', 'ProductsApiController@getAvailability');

	Route::post("{$r['coupons']}", 'CouponsApiController@post');
	Route::put("{$r['coupons']}", 'CouponsApiController@put');
	Route::delete("{$r['coupons']}", 'CouponsApiController@delete');

});
