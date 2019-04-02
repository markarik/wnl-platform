<?php

use Illuminate\Support\Facades\Input;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of the routes that are handled
| by your application. Just tell Laravel the URIs it should respond
| to using a Closure or controller method. Build something great!
|
*/

Auth::routes();

Route::redirect('/voucher', '/payment/voucher', 301);

Route::group(['namespace' => 'Payment', 'prefix' => 'payment', 'middleware' => 'signups-open'], function () {
	Route::redirect('/select-product', '/payment/account', 301)->name('payment-select-product');
	Route::redirect('/', '/payment/account', 301);

	Route::get('account', 'AccountController@index')->name('payment-account');
	Route::post('account', 'AccountController@handleRegister')->name('payment-account-post');

	Route::get('voucher', 'VoucherController@index')->name('payment-voucher');
	Route::post('voucher', 'VoucherController@handle')->name('payment-voucher-post');

	Route::group(['middleware' => 'payment-auth'], function () {
		Route::get('personal-data/{productSlug?}', 'PersonalDataController@index')->name('payment-personal-data');
		Route::post('personal-data', 'PersonalDataController@handle')->name('payment-personal-data-post');

		Route::get('confirm-order', 'ConfirmOrderController@index')->name('payment-confirm-order');
		Route::post('confirm-order', 'ConfirmOrderController@handle')->name('payment-confirm-order-post');
	});
});

Route::group(['middleware' => 'auth'], function () {

	Route::get('/terms', 'TermsOfUseController@index')->name('terms');
	Route::post('/terms', 'TermsOfUseController@accept')->name('terms-accept');

	Route::get('/', function () {
		return redirect('/app');
	})->name('home')->middleware('terms');

	// Using front-end routing for the main application
	Route::get('/app/{path?}', 'AppController@index')
		->name('app')
		->where('path', '(.*)')
		->middleware('terms');

	//Ajax common route
	Route::match(['get', 'post'], '/ax', function () {
		abort_unless(Input::has('controller') && Input::has('method'), 404);
		$controller = Input::get('controller');
		$method = Input::get('method');

		return App::make('App\\Http\\Controllers\\Ajax\\' . $controller)->{$method}();
	});

});

Route::group(['namespace' => 'Admin', 'prefix' => 'admin', 'middleware' => 'admin'], function () {
	Route::group(['namespace' => 'Invoice', 'prefix' => 'invoice'], function () {
		Route::get('advance', function () {
			return Response::view('payment/invoices/advance');
		});
		Route::get('final', function () {
			return Response::view('payment/invoices/final');
		});
		Route::get('pro-forma', function () {
			return Response::view('payment/invoices/pro-forma');
		});
	});
	Route::get('/styleguide', function () {
		return Response::view('styleguide');
	});
	Route::get('/version', function () {
		return Response::view('version', ['laravel' => app()]);
	});
	Route::get('/email/{template}', function ($template) {
		return Response::view('mail/' . $template);
	});
	Route::get('/newsletter/{template}', function ($template) {
		return Response::view('mail/newsletter/' . $template);
	});
	Route::get('/upload-slides', 'UploadSlidesController@index')->name('admin-upload-slides');
	Route::post('/upload-slides', 'UploadSlidesController@handle')->name('admin-upload-slides-post');

	Route::get('/piggyback/{userId}', 'PiggybackController@index');

	// Using front-end routing for the admin panel application
	Route::get('/app/{path?}', 'AppController@index')->where('path', '(.*)');

	Route::get('/update-charts', function () {
		Artisan::queue('charts:update', ['--notify' => true]);
	});
});
