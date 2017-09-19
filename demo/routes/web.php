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

Route::get('/login', '\Demo\App\Http\Controllers\Auth\LoginController@showLoginForm');
Route::post('/login/demo', '\Demo\App\Http\Controllers\Auth\LoginController@login');
Route::post('/logout', '\Demo\App\Http\Controllers\Auth\LoginController@logout');

Route::get('/login/admin', '\App\Http\Controllers\Auth\LoginController@showLoginForm');
Route::post('/login', '\App\Http\Controllers\Auth\LoginController@login');

Route::get('/styleguide', function () {
	return Response::view('styleguide');
});

Route::group(['middleware' => 'auth'], function () {

	Route::get('/', function () {
		return redirect('/app');
	})->name('home');

	// Using front-end routing for the main application
	Route::get('/app/{path?}', 'AppController@index')->name('app')->where('path', '(.*)');

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

Route::group(['namespace' => 'Course', 'middleware' => 'auth'], function () {
	Route::get('/slideshow-builder/{screenId}', 'SlideShowController@build')->name('slideshow-builder');
});

Route::group(['namespace' => 'Admin', 'prefix' => 'admin', 'middleware' => 'auth'], function () {
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

	Route::get('/update-charts', function() { Artisan::queue('charts:update', ['--notify' => true]); });
	Route::get('/update-charts/{slideId}', function($id) { Artisan::call('charts:update', ['id' => $id]); });
});
