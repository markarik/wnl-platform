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

Route::group(['namespace' => 'Api', 'middleware' => 'api-auth'], function () {
	$r = config('papi.resources');

	// Courses
	Route::get("{$r['courses']}/{id}", 'CoursesApiController@get');

	// Lessons
	Route::get("{$r['lessons']}/{id}", 'LessonsApiController@get');

	// Screens
	Route::get("{$r['screens']}/{id}", 'ScreensApiController@get');

	// Users
	Route::get("{$r['users']}/current", 'UsersApiController@getCurrentUser');
	Route::get("{$r['users']}/{id}", 'UsersApiController@get');

	// Editions
	Route::get("{$r['editions']}/{id}", 'EditionsApiController@get');

	// User Progress
//	Route::get("{$r['users']}/{id}", 'CoursesApiController@get');
//	Route::put("{$r['users']}/{id}", 'CoursesApiController@put');


});
