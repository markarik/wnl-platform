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

	// Search
	Route::get("{resource}/.search", function($resource) {
		$controller = 'App\Http\Controllers\Api\PrivateApi\\' . studly_case($resource) . 'ApiController';
		return App::make($controller)->search(App::make('request'));
	});

	// Courses
	Route::get("{$r['courses']}/{id}", 'CoursesApiController@get');

	// Groups
	Route::get("{$r['groups']}/{id}", 'GroupsApiController@get');

	// Lessons
	Route::get("{$r['lessons']}/{id}", 'LessonsApiController@get');
	Route::put("{$r['lessons']}/{id}", 'LessonsApiController@put');

	// Screens
	Route::post("{$r['screens']}", 'ScreensApiController@post');
	Route::get("{$r['screens']}/{id}", 'ScreensApiController@get');
	Route::put("{$r['screens']}/{id}", 'ScreensApiController@put');
	Route::patch("{$r['screens']}/{id}", 'ScreensApiController@patch');
	Route::delete("{$r['screens']}/{id}", 'ScreensApiController@delete');
	Route::post("{$r['screens']}/.search", 'ScreensApiController@filter');

	// Editions
	Route::get("{$r['editions']}/{id}", 'EditionsApiController@get');

	// Slides
	Route::get("{$r['slides']}/{id}", 'SlidesApiController@get');
	Route::put("{$r['slides']}/{id}", 'SlidesApiController@put');
	Route::post("{$r['slides']}/.search", 'SlidesApiController@filter');

	// Presentables
	Route::post("{$r['presentables']}/.search", 'PresentablesApiController@filter');
	Route::get("{$r['presentables']}/{id}", 'PresentablesApiController@get');

	// Slideshows
	Route::get("{$r['slideshows']}/{id}", 'SlideshowsApiController@get');

	// Users
	Route::get("{$r['users']}/{id}", 'UsersApiController@get');
	Route::put("{$r['users']}/{id}", 'UsersApiController@put');

	Route::get("{$r['users']}/{id}/{$r['user-profile']}", 'UserProfileApiController@get');
	Route::put("{$r['users']}/{id}/{$r['user-profile']}", 'UserProfileApiController@put');

	Route::post("{$r['users']}/{id}/{$r['user-avatar']}", 'UserAvatarApiController@post');

	Route::get("{$r['users']}/{id}/{$r['user-address']}", 'UserAddressApiController@get');
	Route::put("{$r['users']}/{id}/{$r['user-address']}", 'UserAddressApiController@put');

	Route::get("{$r['users']}/{id}/{$r['user-billing-data']}", 'UserBillingApiController@get');
	Route::put("{$r['users']}/{id}/{$r['user-billing-data']}", 'UserBillingApiController@put');

	Route::get("{$r['users']}/{id}/{$r['user-settings']}", 'UserSettingsApiController@get');
	Route::put("{$r['users']}/{id}/{$r['user-settings']}", 'UserSettingsApiController@put');

	Route::put("{$r['users']}/{id}/{$r['user-password']}", 'UserPasswordApiController@put');

	Route::get("{$r['users']}/{id}/{$r['user-notifications']}", 'UserNotificationApiController@get');
	Route::post("{$r['users']}/{id}/{$r['user-notifications']}/.search", 'UserNotificationApiController@filter');
	Route::patch("{$r['users']}/{id}/{$r['user-notifications']}/{notificationId}", 'UserNotificationApiController@patch');
	Route::patch("{$r['users']}/{id}/{$r['user-notifications']}", 'UserNotificationApiController@patchMany');

	Route::get("{$r['users']}/{id}/{$r['user-state']}/course/{courseId}", 'UserStateApiController@getCourse');
	Route::put("{$r['users']}/{id}/{$r['user-state']}/course/{courseId}", 'UserStateApiController@putCourse');

	Route::get("{$r['users']}/{id}/{$r['user-state']}/course/{courseId}/lesson/{lessonId}", 'UserStateApiController@getLesson');
	Route::put("{$r['users']}/{id}/{$r['user-state']}/course/{courseId}/lesson/{lessonId}", 'UserStateApiController@putLesson');

	Route::get("{$r['users']}/{id}/{$r['user-state']}/quiz/{quizId}", 'UserStateApiController@getQuiz');
	Route::put("{$r['users']}/{id}/{$r['user-state']}/quiz/{quizId}", 'UserStateApiController@putQuiz');

	Route::get("{$r['users']}/{user}/{$r['user-state']}/time", 'UserStateApiController@getTime');
	Route::put("{$r['users']}/{id}/{$r['user-state']}/time", 'UserStateApiController@incrementTime');

	Route::get("{$r['users']}/{user}/{$r['user-reactions']}/{type?}", 'UserReactionsApiController@getReactions');
	Route::get("{$r['users']}/{user}/{$r['user-reactions']}/byCategory/{type?}", 'UserReactionsApiController@getReactionsByCategory');

	// User Profiles
	Route::post("{$r['profiles']}/.search", 'UserProfileApiController@filter');

	// Orders
	Route::get("{$r['orders']}/{id}", 'OrdersApiController@get');

	// Tags
	Route::get("{$r['tags']}/{id}", 'TagsApiController@get');
	Route::post("{$r['tags']}/.search", 'TagsApiController@filter');

	// Q&A Questions
	Route::post($r['questions'], 'QnaQuestionsApiController@post');
	Route::get("{$r['questions']}/{id}", 'QnaQuestionsApiController@get');
	Route::put("{$r['questions']}/{id}", 'QnaQuestionsApiController@put');
	Route::delete("{$r['questions']}/{id}", 'QnaQuestionsApiController@delete');
	Route::post("{$r['questions']}/.search", 'QnaQuestionsApiController@filter');

	// Q&A Answers
	Route::post($r['answers'], 'QnaAnswersApiController@post');
	Route::get("{$r['answers']}/{id}", 'QnaAnswersApiController@get');
	Route::put("{$r['answers']}/{id}", 'QnaAnswersApiController@put');
	Route::delete("{$r['answers']}/{id}", 'QnaAnswersApiController@delete');
	Route::post("{$r['answers']}/.search", 'QnaAnswersApiController@filter');

	// Quiz Sets
	Route::get("{$r['quiz-sets']}/{id}", 'QuizSetsApiController@get');
	Route::post("{$r['quiz-sets']}", 'QuizSetsApiController@post');

	// Quiz Stats
	Route::get("{$r['quiz-sets']}/{id}/stats", 'QuizStatsApiController@get');

	// Quiz Questions
	Route::get("{$r['quiz-questions']}/{id}", 'QuizQuestionsApiController@get');
	Route::post("{$r['quiz-questions']}/.search", 'QuizQuestionsApiController@filter');

	// Comments
	Route::post($r['comments'], 'CommentsApiController@post');
	Route::get("{$r['comments']}/{id}", 'CommentsApiController@get');
	Route::put("{$r['comments']}/{id}", 'CommentsApiController@put');
	Route::delete("{$r['comments']}/{id}", 'CommentsApiController@delete');
	Route::post("{$r['comments']}/.search", 'CommentsApiController@filter');

	// Chat Messages
	Route::post(
		"{$r['chat-rooms']}/{roomName}/{$r['chat-messages']}/.search",
		'ChatMessagesApiController@searchByRoom'
	);

	// Reactions
	Route::post($r['reactions'], 'ReactionsApiController@postMany');
	Route::delete("{$r['reactions']}", 'ReactionsApiController@destroy');

	// Reactables
	Route::post("{$r['reactables']}/.search", 'ReactablesApiController@filter');

	// Public image upload
	Route::post("upload", 'UploadApiController@post');

	// Categories
	Route::get("{$r['categories']}/{id}", 'CategoriesApiController@get');

	// Slideshow builder
	Route::get("{$r['slideshow-builder']}/category/{categoryId}", 'SlideshowBuilderApiController@byCategory');
	Route::get("{$r['slideshow-builder']}/{slideshowId}", 'SlideshowBuilderApiController@get');

	// Events
	Route::post("events/mentions", 'MentionsApiController@post');
});
