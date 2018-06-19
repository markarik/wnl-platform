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
if (!function_exists('api_action')) {
	function api_action($type, $method)
	{
		Route::$type("{resource}/.{$method}", function ($resource) use ($method) {
			$controller = 'App\Http\Controllers\Api\PrivateApi\\' . studly_case($resource) . 'ApiController';

			return App::make($controller)->$method(App::make('request'));
		});
	}
}

Route::group(['namespace' => 'Api\PrivateApi', 'middleware' => ['api-auth', 'api-cache']], function () {
	$r = config('papi.resources');

	// Search (using search engine)
	api_action('get', 'search');

	// Count
	api_action('get', 'count');

	// Faceted search / filtering
	api_action('post', 'filter');

	// Saved active filters
	api_action('post', 'activeFilters');

	// Faceted search available filters
	api_action('post', 'filterList');

	// Query (using main database)
	api_action('post', 'query');

	// Fetch additional routing data basing on various input
	api_action('post', 'context');

	Route::group(['middleware' => 'subscription'], function () use ($r) {
		// User
		Route::patch("{$r['user-delete']}/{userId}", 'DeleteAccountApiController@patch');

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
		Route::post("{$r['screens']}/.search", 'ScreensApiController@query');

		// Editions
		Route::get("{$r['editions']}/{id}", 'EditionsApiController@get');

		// Slides
		Route::get('slides/.updateCharts/{slideId}', 'SlidesApiController@updateCharts');
		Route::get("{$r['slides']}/{id}", 'SlidesApiController@get');
		Route::put("{$r['slides']}/{id}", 'SlidesApiController@put');
		Route::post("{$r['slides']}/{id}/.detach", 'SlidesApiController@detach');
		Route::post("{$r['slides']}", 'SlidesApiController@post');
		Route::post("{$r['slides']}/.search", 'SlidesApiController@query');

		// Presentables
		Route::post("{$r['presentables']}/.search", 'PresentablesApiController@query');
		Route::get("{$r['presentables']}/{id}", 'PresentablesApiController@get');

		// Slideshows
		Route::get("{$r['slideshows']}/{id}", 'SlideshowsApiController@get');

		// Q&A Questions
		Route::post($r['qna-questions'], 'QnaQuestionsApiController@post');
		Route::get("{$r['qna-questions']}/{id}", 'QnaQuestionsApiController@get');
		Route::put("{$r['qna-questions']}/{id}", 'QnaQuestionsApiController@put');
		Route::delete("{$r['qna-questions']}/{id}", 'QnaQuestionsApiController@delete');
		Route::post("{$r['qna-questions']}/.search", 'QnaQuestionsApiController@query');

		// Q&A Answers
		Route::post($r['qna-answers'], 'QnaAnswersApiController@post');
		Route::get("{$r['qna-answers']}/{id}", 'QnaAnswersApiController@get');
		Route::put("{$r['qna-answers']}/{id}", 'QnaAnswersApiController@put');
		Route::delete("{$r['qna-answers']}/{id}", 'QnaAnswersApiController@delete');
		Route::post("{$r['qna-answers']}/.search", 'QnaAnswersApiController@query');

		// Quiz Sets
		Route::get("{$r['quiz-sets']}/{id}", 'QuizSetsApiController@get');
		Route::post("{$r['quiz-sets']}", 'QuizSetsApiController@post');
		Route::post("{$r['quiz-sets']}", 'QuizSetsApiController@post');

		// Comments
		Route::post($r['comments'], 'CommentsApiController@post');
		Route::get("{$r['comments']}/{id}", 'CommentsApiController@get');
		Route::put("{$r['comments']}/{id}", 'CommentsApiController@put');
		Route::delete("{$r['comments']}/{id}", 'CommentsApiController@delete');
		Route::post("{$r['comments']}/.search", 'CommentsApiController@query');
		Route::post("{$r['comments']}/.count", 'CommentsApiController@query');

		// Chat Messages
		Route::post(
			"{$r['chat-messages']}/.getByRooms",
			'ChatMessagesApiController@getByMultipleRooms'
		);
		Route::post(
			"{$r['chat-messages']}/.getWithContext",
			'ChatMessagesApiController@getWithContext'
		);
		// Chat rooms
		Route::get(
			"{$r['chat-rooms']}/.getPrivateRooms",
			'ChatRoomsApiController@getPrivateRooms');
		Route::post(
			"{$r['chat-rooms']}/.createPrivateRoom",
			'ChatRoomsApiController@createPrivateRoom');
		Route::post(
			"{$r['chat-rooms']}/.createPublicRoom",
			'ChatRoomsApiController@createPublicRoom');
		Route::get("{$r['chat-rooms']}/{id}", 'ChatRoomsApiController@get');

		// Slideshow builder
		Route::get("{$r['slideshow-builder']}/category/{categoryId}", 'SlideshowBuilderApiController@byCategory');
		Route::post("{$r['slideshow-builder']}/preview", 'SlideshowBuilderApiController@preview');
		Route::get("{$r['slideshow-builder']}/{slideshowId}", 'SlideshowBuilderApiController@get');
		Route::post("{$r['slideshow-builder']}/.query", 'SlideshowBuilderApiController@query');
		Route::get("{$r['slideshow-builder']}", 'SlideshowBuilderApiController@getEmpty');

		// Quiz Stats
		Route::get("{$r['quiz-sets']}/{id}/stats", 'QuizStatsApiController@get');

		// Quiz Questions Stats
		Route::get("{$r['quiz-questions']}/stats", 'QuizQuestionsApiController@stats');

		// Quiz Questions
		Route::get("{$r['quiz-questions']}/{id}", 'QuizQuestionsApiController@get');
		Route::post("{$r['quiz-questions']}/.search", 'QuizQuestionsApiController@query');
		Route::post("{$r['quiz-questions']}", 'QuizQuestionsApiController@post');
		Route::put("{$r['quiz-questions']}/{id}", 'QuizQuestionsApiController@put');
	});

	// User Lessons
	Route::put("{$r['user-lesson']}/{userId}/batch", 'UserLessonApiController@putBatch');
	Route::put("{$r['user-lesson']}/{userId}", 'UserLessonApiController@putPlan');
	Route::put("{$r['user-lesson']}/{userId}/{lessonId}", 'UserLessonApiController@put');

	// Users
	Route::get("{$r['users']}/{id}", 'UsersApiController@get');
	Route::put("{$r['users']}/{id}", 'UsersApiController@put');

	Route::get("{$r['users']}/{id}/{$r['user-profile']}", 'UserProfilesApiController@get');
	Route::put("{$r['users']}/{id}/{$r['user-profile']}", 'UserProfilesApiController@put');

	Route::post("{$r['users']}/{id}/{$r['user-avatar']}", 'UserAvatarApiController@post');

	Route::get("{$r['users']}/{id}/{$r['user-address']}", 'UserAddressApiController@get');
	Route::put("{$r['users']}/{id}/{$r['user-address']}", 'UserAddressApiController@put');

	Route::get("{$r['users']}/{id}/{$r['user-billing-data']}", 'UserBillingApiController@get');
	Route::put("{$r['users']}/{id}/{$r['user-billing-data']}", 'UserBillingApiController@put');

	Route::get("{$r['users']}/{id}/{$r['user-settings']}", 'UserSettingsApiController@get');
	Route::put("{$r['users']}/{id}/{$r['user-settings']}", 'UserSettingsApiController@put');

	Route::put("{$r['users']}/{id}/{$r['user-password']}", 'UserPasswordApiController@put');

	Route::get("{$r['users']}/{id}/{$r['user-notifications']}", 'UserNotificationApiController@get');
	Route::post("{$r['users']}/{id}/{$r['user-notifications']}/.search", 'UserNotificationApiController@query');
	Route::patch("{$r['users']}/{id}/{$r['user-notifications']}/{notificationId}", 'UserNotificationApiController@patch');
	Route::patch("{$r['users']}/{id}/{$r['user-notifications']}", 'UserNotificationApiController@patchMany');

	Route::get("{$r['users']}/{id}/{$r['user-state']}/course/{courseId}", 'UserStateApiController@getCourse');
	Route::put("{$r['users']}/{id}/{$r['user-state']}/course/{courseId}", 'UserStateApiController@putCourse');
	Route::delete("{$r['users']}/{id}/{$r['user-state']}/course/{courseId}", 'UserStateApiController@deleteCourse');

	Route::get("user_subscription/current", 'UserSubscriptionApiController@getSubscription');

	Route::get("{$r['users']}/{id}/{$r['user-state']}/course/{courseId}/lesson/{lessonId}", 'UserStateApiController@getLesson');
	Route::put("{$r['users']}/{id}/{$r['user-state']}/course/{courseId}/lesson/{lessonId}", 'UserStateApiController@putLesson');

	Route::put("{$r['users']}/{id}/{$r['user-state']}/time", 'UserStateApiController@incrementTime');

	Route::post("{$r['users']}/{user}/{$r['user-state']}/quizPosition", 'UserStateApiController@getQuizPosition');
	Route::put("{$r['users']}/{id}/{$r['user-state']}/quizPosition", 'UserStateApiController@saveQuizPosition');

	Route::get("{$r['users']}/{id}/{$r['user-state']}/stats", 'UserStateApiController@getStats');

	Route::get("{$r['users']}/{user}/{$r['user-reactions']}/{type?}", 'UserReactionsApiController@getReactions');
	Route::get("{$r['users']}/{user}/{$r['user-reactions']}/byCategory/{type?}", 'UserReactionsApiController@getReactionsByCategory');

	Route::delete("{$r['users']}/{userId}/{$r['user-collections']}", 'UserCollectionsApiController@delete');

	// Orders
	Route::get("{$r['orders']}/{id}", 'OrdersApiController@get');
	Route::put("{$r['orders']}/{id}/coupon", 'OrdersApiController@putCoupon');
	Route::get("{$r['orders']}/{id}/.cancel", 'OrdersApiController@cancel');

	// Tags
	Route::get("{$r['tags']}/{id}", 'TagsApiController@get');
	Route::post("{$r['tags']}/.search", 'TagsApiController@query');

	// User Quiz Results
	Route::get("{$r['user-quiz-results']}/{userId}", 'UserQuizResultsApiController@get');
	Route::post("{$r['user-quiz-results']}/{userId}", 'UserQuizResultsApiController@post');
	Route::get("{$r['user-quiz-results']}/{userId}/quiz/{quizId}", 'UserQuizResultsApiController@getQuiz');
	Route::put("{$r['user-quiz-results']}/{userId}/quiz/{quizId}", 'UserQuizResultsApiController@putQuiz');
	Route::delete("{$r['user-quiz-results']}/{userId}", 'UserQuizResultsApiController@delete');

	// Reactions
	Route::post($r['reactions'], 'ReactionsApiController@postMany');
	Route::delete("{$r['reactions']}", 'ReactionsApiController@destroy');

	// Reactables
	Route::post("{$r['reactables']}/.search", 'ReactablesApiController@query');

	// Public image upload
	Route::post("upload", 'UploadApiController@post');

	// Categories
	Route::get("{$r['categories']}/{id}", 'CategoriesApiController@get');

	// Events
	Route::post("events/mentions", 'MentionsApiController@post');

	// Users Plans
	Route::get("{$r['user-plan']}/{userId}", 'UserPlanApiController@get');
	Route::post("{$r['user-plan']}/{userId}", 'UserPlanApiController@post');

	// Tasks
	Route::get("{$r['tasks']}/{id}", 'TasksApiController@get');
	Route::patch("{$r['tasks']}/{id}", 'TasksApiController@patch');

	// Pages
	Route::get("{$r['pages']}/{slug}", 'PagesApiController@get');
});
