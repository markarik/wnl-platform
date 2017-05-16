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

	// Groups
	Route::get("{$r['groups']}/{id}", 'Course\GroupsApiController@get');

	// Lessons
	Route::get("{$r['lessons']}/{id}", 'Course\LessonsApiController@get');

	// Screens
	Route::get("{$r['screens']}/search", 'Course\ScreensApiController@search');
	Route::get("{$r['screens']}/{id}", 'Course\ScreensApiController@get');
	Route::put("{$r['screens']}/{id}", 'Course\ScreensApiController@put');
	Route::post("{$r['screens']}", 'Course\ScreensApiController@post');

	// Editions
	Route::get("{$r['editions']}/{id}", 'Course\EditionsApiController@get');

	// Slides
	Route::get("{$r['slides']}/{id}", 'Course\SlidesApiController@get');
	Route::put("{$r['slides']}/{id}", 'Course\SlidesApiController@put');

	// Presentables
	Route::get("{$r['presentables']}/search", 'Course\PresentablesApiController@search');
	Route::get("{$r['presentables']}/{id}", 'Course\PresentablesApiController@get');

	// Users
	Route::get("{$r['users']}/{id}", 'User\UsersApiController@get');
	Route::put("{$r['users']}/{id}", 'User\UsersApiController@put');

	Route::get("{$r['users']}/{id}/{$r['user-profile']}", 'User\UserProfileApiController@get');
	Route::put("{$r['users']}/{id}/{$r['user-profile']}", 'User\UserProfileApiController@put');

	Route::post("{$r['users']}/{id}/{$r['user-avatar']}", 'User\UserAvatarApiController@post');

	Route::get("{$r['users']}/{id}/{$r['user-address']}", 'User\UserAddressApiController@get');
	Route::put("{$r['users']}/{id}/{$r['user-address']}", 'User\UserAddressApiController@put');

	Route::get("{$r['users']}/{id}/{$r['user-billing-data']}", 'User\UserBillingApiController@get');
	Route::put("{$r['users']}/{id}/{$r['user-billing-data']}", 'User\UserBillingApiController@put');

	Route::get("{$r['users']}/{id}/{$r['user-settings']}", 'User\UserSettingsApiController@get');
	Route::put("{$r['users']}/{id}/{$r['user-settings']}", 'User\UserSettingsApiController@put');

	Route::put("{$r['users']}/{id}/{$r['user-password']}", 'User\UserPasswordApiController@put');

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

	// Quiz Sets
	Route::get("{$r['quiz-sets']}/{id}", 'Quiz\QuizSetsApiController@get');
	Route::post("{$r['quiz-sets']}", 'Quiz\QuizSetsApiController@post');

	// User Progress
//	Route::get("{$r['users']}/{id}", 'CoursesApiController@get');
//	Route::put("{$r['users']}/{id}", 'CoursesApiController@put');

});
