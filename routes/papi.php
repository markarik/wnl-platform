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
	Route::put("{$r['lessons']}/{id}", 'Course\LessonsApiController@put');

	// Screens
	Route::post("{$r['screens']}", 'Course\ScreensApiController@post');
	Route::get("{$r['screens']}/{id}", 'Course\ScreensApiController@get');
	Route::put("{$r['screens']}/{id}", 'Course\ScreensApiController@put');
	Route::patch("{$r['screens']}/{id}", 'Course\ScreensApiController@patch');
	Route::delete("{$r['screens']}/{id}", 'Course\ScreensApiController@delete');
	Route::post("{$r['screens']}/.search", 'Course\ScreensApiController@search');

	// Editions
	Route::get("{$r['editions']}/{id}", 'Course\EditionsApiController@get');

	// Slides
	Route::get("{$r['slides']}/{id}", 'Course\SlidesApiController@get');
	Route::put("{$r['slides']}/{id}", 'Course\SlidesApiController@put');

	// Presentables
	Route::post("{$r['presentables']}/.search", 'Course\PresentablesApiController@search');
	Route::get("{$r['presentables']}/{id}", 'Course\PresentablesApiController@get');

	// Slideshows
	Route::get("{$r['slideshows']}/{id}", 'Course\SlideshowsApiController@get');

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

	Route::get("{$r['users']}/{id}/{$r['user-notifications']}", 'User\UserNotificationApiController@get');
	Route::patch("{$r['users']}/{id}/{$r['user-notifications']}", 'User\UserNotificationApiController@patch');

	Route::get("{$r['users']}/{id}/{$r['user-state']}/course/{courseId}", 'User\UserStateApiController@getCourse');
	Route::patch("{$r['users']}/{id}/{$r['user-state']}/course/{courseId}", 'User\UserStateApiController@patchCourse');

	Route::get("{$r['users']}/{id}/{$r['user-state']}/course/{courseId}/lesson/{lessonId}", 'User\UserStateApiController@getLesson');
	Route::patch("{$r['users']}/{id}/{$r['user-state']}/course/{courseId}/lesson/{lessonId}", 'User\UserStateApiController@patchLesson');

	// Orders
	Route::get("{$r['orders']}/{id}", 'OrdersApiController@get');

	// Tags
	Route::get("{$r['tags']}/{id}", 'TagsApiController@get');

	// Q&A Questions
	Route::post($r['questions'], 'Qna\QuestionsApiController@post');
	Route::get("{$r['questions']}/{id}", 'Qna\QuestionsApiController@get');
	Route::put("{$r['questions']}/{id}", 'Qna\QuestionsApiController@put');
	Route::delete("{$r['questions']}/{id}", 'Qna\QuestionsApiController@delete');
	Route::post("{$r['questions']}/.search", 'Qna\QuestionsApiController@search');

	// Q&A Answers
	Route::post($r['answers'], 'Qna\AnswersApiController@post');
	Route::get("{$r['answers']}/{id}", 'Qna\AnswersApiController@get');
	Route::put("{$r['answers']}/{id}", 'Qna\AnswersApiController@put');
	Route::delete("{$r['answers']}/{id}", 'Qna\AnswersApiController@delete');
	Route::post("{$r['answers']}/.search", 'Qna\AnswersApiController@search');

	// Quiz Sets
	Route::get("{$r['quiz-sets']}/{id}", 'Quiz\QuizSetsApiController@get');
	Route::post("{$r['quiz-sets']}", 'Quiz\QuizSetsApiController@post');

	// Comments
	Route::post($r['comments'], 'CommentsApiController@post');
	Route::get("{$r['comments']}/{id}", 'CommentsApiController@get');
	Route::put("{$r['comments']}/{id}", 'CommentsApiController@put');
	Route::delete("{$r['comments']}/{id}", 'CommentsApiController@delete');
	Route::post("{$r['comments']}/.search", 'CommentsApiController@search');

	// Chat Messages
	Route::post(
		"{$r['chat-rooms']}/{roomName}/{$r['chat-messages']}/.search",
		'Chat\ChatMessagesApiController@searchByRoom'
	);

	// Reactions
	Route::post($r['reactions'], 'ReactionsApiController@post');
	Route::delete("{$r['reactions']}", 'ReactionsApiController@destroy');

	// User Progress
//	Route::get("{$r['users']}/{id}", 'CoursesApiController@get');
//	Route::put("{$r['users']}/{id}", 'CoursesApiController@put');

});
