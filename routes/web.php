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
		return redirect('/course/1');
	})->name('home');

	Route::get('/course/1', 'Course\CourseController@index');

	//Route::get( '/course/1/module/{moduleId}/chapter/{chapterId}', 'ModuleController@index' );

	Route::get('/course/1/module/{moduleId}/chapter/{chapterId}/section/{sectionId}', 'ChapterController@index');

	Route::get('/dashboard', 'User\DashboardController@index');
	Route::get('/profile/orders', 'User\OrderController@index')->name('profile-orders');

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
