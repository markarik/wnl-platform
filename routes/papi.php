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

Route::group(['namespace' => 'Api', 'middleware' => 'auth'], function() {
	$resources = Config::get('papi.resources');

	Route::get(sprintf('%s/current', $resources['users']), 'UsersApiController@getCurrentUser');

	Route::get(sprintf('%s/{id}/nav', $resources['courses']), 'CoursesApiController@getNavigation');

	// Lessons
	Route::get(sprintf('%s/{id}/nav', $resources['lessons']), 'LessonsApiController@getNavigation');
	Route::get(sprintf('%s/{id}', $resources['screens']), 'LessonsApiController@getScreen');

	// Forms
	Route::get(sprintf('%s/personal-info/{userId?}', $resources['forms']), 'Forms\PersonalInfoFormController@getForm');
});
