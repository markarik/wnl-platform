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

Route::get('/styleguide', function() {
	return Response::view('styleguide');
});

Route::group(['namespace' => 'Payment', 'prefix' => 'payment', 'middleware' => 'payment'], function ()
{
	Route::get('select-product', 'SelectProductController@index')->name('payment-select-product');

	Route::get('personal-data/{product?}', 'PersonalDataController@index')->name('payment-personal-data');
	Route::post('personal-data', 'PersonalDataController@handle')->name('payment-personal-data-post');

	Route::get('confirm-order', 'ConfirmOrderController@index')->name('payment-confirm-order');
	Route::post('confirm-order', 'ConfirmOrderController@handle')->name('payment-confirm-order-post');
});


Route::group(['middleware' => 'auth'], function () {

	Route::get('/', function () {
		return redirect('/app');
	})->name('home');
	//
	// Route::get('/dashboard', 'User\DashboardController@index');
	Route::get('/profile/orders', 'User\OrderController@index')->name('profile-orders');

	// Using front-end routing for the main application
	Route::get('/app/{path?}', function () {
		return view('layouts.app');
	})->name('app')->where('path', '(.*)');

	/*
	* Ajax common route
	*/
	Route::match(['get', 'post'], '/ax', function () {
		abort_unless(Input::has('controller') && Input::has('method'), 404);
		$controller = Input::get('controller');
		$method = Input::get('method');

		return App::make('App\\Http\\Controllers\\Ajax\\' . $controller)->{$method}();
	});

});

// Route::group(['namespace' => 'Course', 'middleware' => 'auth'], function () {
// 	Route::get('/course/{courseId}', 'CourseController@index')->name('course');
// 	Route::get('/course/{courseId}/lesson/{lessonId}', 'LessonController@index')->name('lesson');
// 	Route::get('/course/{courseId}/lesson/{lessonId}/snippet/{snippetId}/slide#/{slideNumber}', 'LessonController@index')
// 		->name('section');
//
// 	Route::get('/slideshow-builder/{snippetId}', 'SlideShowController@build')->name('slideshow-builder');
// });

Route::group(['namespace' => 'Admin', 'prefix' => 'admin', 'middleware' => 'auth'], function ()
{
	Route::get('/upload-slides', 'UploadSlidesController@index')->name('admin-upload-slides');
	Route::post('/upload-slides', 'UploadSlidesController@handle')->name('admin-upload-slides-post');
});
