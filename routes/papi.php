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

Route::group(['namespace' => 'Api', 'middleware' => 'auth'], function () {
	$resources = Config::get('papi.resources');

	// Users
	Route::get(sprintf('%s/current', $resources['users']), 'UsersApiController@getCurrentUser');

	// Courses
	Route::get(sprintf('%s/{id}/nav', $resources['courses']), 'CoursesApiController@getNavigation');

	// Lessons
	Route::get(sprintf('%s/{id}/nav', $resources['lessons']), 'LessonsApiController@getNavigation');
	Route::get(sprintf('%s/{id}', $resources['screens']), 'LessonsApiController@getScreen');

	// Editions
	Route::get(
		sprintf('%s/{editionId}/%s/{lessonId}', $resources['editions'], $resources['lesson-availability']),
		'EditionsApiController@getWithLessonAvailability'
	);
	Route::get(
		sprintf('%s/{editionId}/%s/{userId}', $resources['editions'], $resources['user-progress']),
		'EditionsApiController@getWithUserProgress'
	);
	Route::put(
		sprintf('%s/{editionId}/%s/{userId}', $resources['editions'], $resources['user-progress']),
		'EditionsApiController@putUserProgress'
	);

	// Forms
	Route::get(sprintf('%s/personal-info/{userId?}', $resources['forms']), 'Forms\PersonalInfoFormController@getForm');
});
