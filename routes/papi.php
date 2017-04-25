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

Route::group(['namespace' => 'Api\PrivateApi', 'middleware' => 'api-auth'], function () {
	$r = config('papi.resources');

	// Courses
	Route::get("{$r['courses']}/{id}", 'Course\CoursesApiController@get');

	// Lessons
	Route::get("{$r['lessons']}/{id}", 'Course\LessonsApiController@get');

	// Screens
	Route::get("{$r['screens']}/{id}", 'Course\ScreensApiController@get');

	// Editions
	Route::get("{$r['editions']}/{id}", 'Course\EditionsApiController@get');

	// Users
	Route::get("{$r['users']}/{id}", 'User\UsersApiController@get');
	Route::put("{$r['users']}/{id}", 'User\UsersApiController@put');

	Route::get("{$r['users']}/{id}/{$r['user-profile']}", 'User\UsersApiController@getUserProfile');
	Route::put("{$r['users']}/{id}/{$r['user-profile']}", 'User\UsersApiController@putUserProfile');

	Route::post("{$r['users']}/{id}/{$r['user-avatar']}", 'User\UsersApiController@putUserAvatar');

	// Orders
	Route::get("{$r['orders']}/{id}", 'OrdersApiController@get');

	// Tags
	Route::get("{$r['tags']}/{id}", 'TagsApiController@get');

	// Questions
	Route::get("{$r['questions']}/{id}", 'Qna\QuestionsApiController@get');
	Route::post($r['questions'], 'Qna\QuestionsApiController@post');

	// Answers
	Route::get("{$r['answers']}/{id}", 'Qna\AnswersApiController@get');
	Route::post($r['answers'], 'Qna\AnswersApiController@post');

	// User Progress
//	Route::get("{$r['users']}/{id}", 'CoursesApiController@get');
//	Route::put("{$r['users']}/{id}", 'CoursesApiController@put');


});
