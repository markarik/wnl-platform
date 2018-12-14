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

Route::group(['namespace' => 'Api\PrivateApi', 'middleware' => ['api-auth']], function () {
	$r = config('papi.resources');

	Route::group(['middleware' => ['admin']], function () use ($r) {
		// Courses
		Route::put("{$r['courses']}/{id}", 'CoursesApiController@put');
		Route::get("{$r['courses']}/{id}", 'CoursesApiController@get');

		// Flashcards admin
		Route::put("{$r['flashcards-sets']}/{id}", 'FlashcardsSetsApiController@put');
		Route::post("{$r['flashcards-sets']}", 'FlashcardsSetsApiController@post');
		Route::put("{$r['flashcards']}/{id}", 'FlashcardsApiController@put');
		Route::post("{$r['flashcards']}", 'FlashcardsApiController@post');
		Route::post("{$r['flashcards']}/.filter", 'FlashcardsApiController@filter');
		Route::post("{$r['flashcards-sets']}/.filter", 'FlashcardsSetsApiController@filter');

		//Users admin
		Route::post("{$r['users']}/.filter", 'UsersApiController@filter');
		Route::get("{$r['users']}/{id}", 'UsersApiController@get');
		Route::put("{$r['users']}/{id}", 'UsersApiController@put');
		Route::post("{$r['users']}", 'UsersApiController@post');

		//Users Plans
		Route::get("{$r['user-lesson']}/{userId}", 'UserLessonApiController@getForUser');

		//Site wide messages
		Route::get("{$r['site-wide-messages']}/dashboard_news", 'SiteWideMessagesApiController@getDashboardNews');
		Route::get("{$r['site-wide-messages']}/{id}", 'SiteWideMessagesApiController@get');
		Route::put("{$r['site-wide-messages']}/{id}", 'SiteWideMessagesApiController@put');
		Route::post("{$r['site-wide-messages']}", 'SiteWideMessagesApiController@post');

		// Quiz Questions
		Route::post("{$r['quiz-questions']}", 'QuizQuestionsApiController@post');
		Route::put("{$r['quiz-questions']}/{id}", 'QuizQuestionsApiController@put');
		Route::delete("{$r['quiz-questions']}/{id}", 'QuizQuestionsApiController@delete');
		Route::put("{$r['quiz-questions']}/{id}/restore", 'QuizQuestionsApiController@restore');
		Route::get("{$r['quiz-questions']}/trashed/{id}", 'QuizQuestionsApiController@getWithTrashed');

		// Orders
		Route::post("{$r['orders']}/.filter", 'OrdersApiController@filter');

		// Groups
		Route::post("{$r['groups']}/.filter", 'GroupsApiController@filter');
		Route::post("{$r['groups']}", 'GroupsApiController@post');
		Route::put("{$r['groups']}/{id}", 'GroupsApiController@put');

		// Lessons
		Route::post("{$r['lessons']}", 'LessonsApiController@post');
		Route::put("{$r['lessons']}/{id}", 'LessonsApiController@put');
	});

	// Count
	api_action('get', 'count');

	// Saved active filters
	api_action('post', 'activeFilters');

	// Faceted search available filters
	api_action('post', 'filterList');

	// Fetch additional routing data basing on various input
	api_action('post', 'context');

	Route::group(['middleware' => ['account-status', 'subscription']], function () use ($r) {
		// Courses
		Route::get("{$r['courses']}/{id}/structure", 'CoursesApiController@getStructure');

		// Groups
		Route::get("{$r['groups']}/{id}", 'GroupsApiController@get');

		// Lessons
		Route::get("{$r['lessons']}/{id}/screens", 'LessonsApiController@getScreens');
		Route::get("{$r['lessons']}/{id}", 'LessonsApiController@get');

		// Screens
		Route::post("{$r['screens']}", 'ScreensApiController@post');
		Route::get("{$r['screens']}/{id}", 'ScreensApiController@get');
		Route::put("{$r['screens']}/{id}", 'ScreensApiController@put');
		Route::patch("{$r['screens']}/{id}", 'ScreensApiController@patch');
		Route::delete("{$r['screens']}/{id}", 'ScreensApiController@delete');

		// Slides
		Route::get("{$r['slides']}/.search", 'SlidesApiController@search');
		Route::get('slides/.updateCharts/{slideId}', 'SlidesApiController@updateCharts');
		Route::get("{$r['slides']}/{id}", 'SlidesApiController@get');
		Route::put("{$r['slides']}/{id}", 'SlidesApiController@put');
		Route::post("{$r['slides']}/{id}/.detach", 'SlidesApiController@detach');
		Route::post("{$r['slides']}", 'SlidesApiController@post');
		Route::post(
			"{$r['slides']}/category/{tagName}",
			'SlidesApiController@getFromCategoryByTagName'
		);

		// Presentables
		Route::post("{$r['presentables']}/slides", 'PresentablesApiController@getSlides');
		Route::post("{$r['presentables']}/slides/byOrderNumber", 'PresentablesApiController@getSlideByOrderNumber');
		Route::get("{$r['presentables']}/{id}", 'PresentablesApiController@get');

		// Slideshows
		Route::get("{$r['slideshows']}/{id}", 'SlideshowsApiController@get');

		// Q&A Questions
		Route::post($r['qna-questions'], 'QnaQuestionsApiController@post');
		Route::get("{$r['qna-questions']}/latest", 'QnaQuestionsApiController@getLatest');
		Route::post("{$r['qna-questions']}/byIds", 'QnaQuestionsApiController@getByIds');
		Route::post("{$r['qna-questions']}/byTags", 'QnaQuestionsApiController@getByTags');
		Route::get("{$r['qna-questions']}/query", 'QnaQuestionsApiController@query');
		Route::get("{$r['qna-questions']}/{id}", 'QnaQuestionsApiController@get');
		Route::put("{$r['qna-questions']}/{id}", 'QnaQuestionsApiController@put');
		Route::delete("{$r['qna-questions']}/{id}", 'QnaQuestionsApiController@delete');

		// Q&A Answers
		Route::post($r['qna-answers'], 'QnaAnswersApiController@post');
		Route::get("{$r['qna-answers']}/query", 'QnaAnswersApiController@query');
		Route::get("{$r['qna-answers']}/{id}", 'QnaAnswersApiController@get');
		Route::put("{$r['qna-answers']}/{id}", 'QnaAnswersApiController@put');
		Route::delete("{$r['qna-answers']}/{id}", 'QnaAnswersApiController@delete');

		// Quiz Sets
		Route::get("{$r['quiz-sets']}/{id}", 'QuizSetsApiController@get');
		Route::post("{$r['quiz-sets']}", 'QuizSetsApiController@post');
		Route::post("{$r['quiz-sets']}", 'QuizSetsApiController@post');

		// Comments
		Route::post($r['comments'], 'CommentsApiController@post');
		Route::get("{$r['comments']}/query", 'CommentsApiController@query');
		Route::get("{$r['comments']}/{id}", 'CommentsApiController@get');
		Route::put("{$r['comments']}/{id}", 'CommentsApiController@put');
		Route::delete("{$r['comments']}/{id}", 'CommentsApiController@delete');

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
		Route::post("{$r['slideshow-builder']}/category/{categoryId}/.searchBySlides/", 'SlideshowBuilderApiController@byCategorySlides');
		Route::get("{$r['slideshow-builder']}/slide/{slideId}/", 'SlideshowBuilderApiController@bySlideId');
		Route::post("{$r['slideshow-builder']}/preview", 'SlideshowBuilderApiController@preview');
		Route::get("{$r['slideshow-builder']}/{slideshowId}", 'SlideshowBuilderApiController@get');
		Route::get("{$r['slideshow-builder']}", 'SlideshowBuilderApiController@getEmpty');


		// Quiz Stats
		Route::get("{$r['quiz-sets']}/{id}/stats", 'QuizStatsApiController@get');

		// Quiz Questions Stats
		Route::get("{$r['quiz-questions']}/stats", 'QuizQuestionsApiController@stats');

		// Quiz Questions
		Route::post("{$r['quiz-questions']}/.filter", 'QuizQuestionsApiController@filter');
		Route::get("{$r['quiz-questions']}/{id}", 'QuizQuestionsApiController@get');
		Route::post("{$r['quiz-questions']}/byTagName", 'QuizQuestionsApiController@getByTagName');

		// Flashcards
		Route::get("{$r['flashcards-sets']}/{id}", 'FlashcardsSetsApiController@get');
		Route::get("{$r['flashcards']}/{id}", 'FlashcardsApiController@get');

		// Flashcard results
		Route::post("{$r['user-flashcards-results']}/{userId}/{flashcardId}", 'UserFlashcardsResultsApiController@post');
		Route::post("{$r['user-flashcards-results']}/{userId}", 'UserFlashcardsResultsApiController@fetchMany');

		// Flashcard notes
		Route::post("{$r['user-flashcard-notes']}/{flashcardId}", 'UserFlashcardNotesApiController@post');
		Route::put("{$r['user-flashcard-notes']}/{flashcardId}/{userFlashcardNoteId}", 'UserFlashcardNotesApiController@put');
	});

	Route::get("{$r['roles']}/{id}", 'RolesApiController@get');

	// Certificates
	Route::get("{$r['certificates']}", 'CertificatesApiController@getAvailableCertificates');
	Route::get("{$r['certificates']}/participation/{id}", 'CertificatesApiController@getParticipationCertificate');
	Route::get("{$r['certificates']}/final/{id}", 'CertificatesApiController@getFinalCertificate');

	// User Lessons
	Route::put("{$r['user-lesson']}/{userId}/batch", 'UserLessonApiController@putBatch');
	Route::put("{$r['user-lesson']}/{userId}", 'UserLessonApiController@putPlan');
	Route::put("{$r['user-lesson']}/{userId}/{lessonId}", 'UserLessonApiController@put');

	Route::get("{$r['users']}/{id}/{$r['user-profile']}", 'UserProfilesApiController@get');
	Route::put("{$r['users']}/{id}/{$r['user-profile']}", 'UserProfilesApiController@put');

	Route::get("{$r['profiles']}/.search", "UserProfilesApiController@search");

	Route::post("{$r['users']}/{id}/{$r['user-avatar']}", 'UserAvatarApiController@post');

	Route::get("{$r['users']}/{id}/{$r['user-address']}", 'UserAddressApiController@get');
	Route::put("{$r['users']}/{id}/{$r['user-address']}", 'UserAddressApiController@put');

	Route::get("{$r['users']}/{id}/{$r['user-billing-data']}", 'UserBillingApiController@get');
	Route::put("{$r['users']}/{id}/{$r['user-billing-data']}", 'UserBillingApiController@put');

	Route::get("{$r['users']}/{id}/{$r['user-settings']}", 'UserSettingsApiController@get');
	Route::put("{$r['users']}/{id}/{$r['user-settings']}", 'UserSettingsApiController@put');

	Route::patch("{$r['users']}/{userId}/forget", 'UsersApiController@forget');

	Route::get("{$r['users']}/{userId}/{$r['user-personal-data']}", 'UserPersonalDataApiController@get');
	Route::post("{$r['users']}/{userId}/{$r['user-personal-data']}", 'UserPersonalDataApiController@post');

	Route::put("{$r['users']}/{id}/{$r['user-password']}", 'UserPasswordApiController@put');

	Route::get("{$r['users']}/{id}/{$r['user-notifications']}", 'UserNotificationApiController@get');
	Route::post("{$r['users']}/{id}/{$r['user-notifications']}/query", 'UserNotificationApiController@queryForUser');
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
	Route::get(
		"{$r['users']}/{user}/{$r['user-reactions']}/byCategory/{type?}",
		'UserReactionsApiController@getReactionsByCategory'
	);

	Route::delete("{$r['users']}/{userId}/{$r['user-collections']}", 'UserCollectionsApiController@delete');

	// Orders
	Route::get("{$r['orders']}/{id}", 'OrdersApiController@get');
	Route::put("{$r['orders']}/{id}/coupon", 'OrdersApiController@putCoupon');
	Route::get("{$r['orders']}/{id}/.cancel", 'OrdersApiController@cancel');

	// Invoices
	Route::get("{$r['invoices']}/{id}", 'InvoicesApiController@get');

	// Payments
	Route::post("{$r['payments']}", 'PaymentsApiController@post');

	// Tags
	Route::get("{$r['tags']}/{id}", 'TagsApiController@get');
	Route::post("{$r['tags']}/byName", 'TagsApiController@byName');

	// User Quiz Results
	Route::get("{$r['user-quiz-results']}/{userId}", 'UserQuizResultsApiController@get');
	Route::post("{$r['user-quiz-results']}/{userId}", 'UserQuizResultsApiController@post');
	Route::get("{$r['user-quiz-results']}/{userId}/quiz/{quizId}", 'UserQuizResultsApiController@getQuiz');
	Route::put("{$r['user-quiz-results']}/{userId}/quiz/{quizId}", 'UserQuizResultsApiController@putQuiz');
	Route::delete("{$r['user-quiz-results']}/{userId}", 'UserQuizResultsApiController@delete');

	// Annotations
	Route::post("{$r['annotations']}/.filter", 'AnnotationsApiController@filter');
	Route::get("{$r['annotations']}/{id}", 'AnnotationsApiController@get');
	Route::post("{$r['annotations']}", 'AnnotationsApiController@post');
	Route::put("{$r['annotations']}/{id}", 'AnnotationsApiController@put');
	Route::delete("{$r['annotations']}/{id}", 'AnnotationsApiController@delete');

	// Reactions
	Route::post($r['reactions'], 'ReactionsApiController@postMany');
	Route::delete("{$r['reactions']}", 'ReactionsApiController@destroy');

	// Reactables
	Route::post("{$r['reactables']}/{userId}/savedSlides", 'ReactablesApiController@getSavedSlidesForUser');

	// Public image upload
	Route::post("upload", 'UploadApiController@post');

	// Categories
	Route::get("{$r['categories']}/{id}", 'CategoriesApiController@get');

	// Events
	Route::post("events/mentions", 'MentionsApiController@post');

	// Users Plans
	Route::get("{$r['user-plan']}/{userId}", 'UserPlanApiController@get');
	Route::post("{$r['user-plan']}/{userId}", 'UserPlanApiController@post');

	Route::get("{$r['users']}/{userId}/{$r['site-wide-messages']}", 'SiteWideMessagesApiController@getForUser');
	Route::put("{$r['site-wide-messages']}/{messageId}/read", 'SiteWideMessagesApiController@read');

	// Tasks
	Route::post("{$r['tasks']}/.filter", 'TasksApiController@filter');
	Route::get("{$r['tasks']}/{id}", 'TasksApiController@get');
	Route::patch("{$r['tasks']}/{id}", 'TasksApiController@patch');

	// Pages
	Route::get("{$r['pages']}/{slug}", 'PagesApiController@get');
});
