<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
	/**
	 * The event listener mappings for the application.
	 *
	 * @var array
	 */
	protected $listen = [
		'App\Events\QnaQuestionPosted' => [
			'App\Listeners\UserNotificationsGate',
			'App\Listeners\ContentUpdatesGate',
		],

		'App\Events\QnaQuestionRemoved' => [
			'App\Listeners\UserNotificationsGate',
			'App\Listeners\ContentUpdatesGate',
		],

		'App\Events\QnaQuestionRestored' => [
			'App\Listeners\UserNotificationsGate',
			'App\Listeners\ContentUpdatesGate',
		],

		'App\Events\QnaAnswerPosted' => [
			'App\Listeners\UserNotificationsGate',
			'App\Listeners\ContentUpdatesGate',
		],

		'App\Events\QnaAnswerRemoved' => [
			'App\Listeners\UserNotificationsGate',
			'App\Listeners\ContentUpdatesGate',
		],

		'App\Events\CommentPosted' => [
			'App\Listeners\UserNotificationsGate',
			'App\Listeners\ContentUpdatesGate',
		],

		'App\Events\CommentRemoved' => [
			'App\Listeners\UserNotificationsGate',
			'App\Listeners\ContentUpdatesGate',
		],

		'App\Events\CommentRestored' => [
			'App\Listeners\UserNotificationsGate',
			'App\Listeners\ContentUpdatesGate',
		],

		'App\Events\ReactionAdded' => [
			'App\Listeners\UserNotificationsGate',
			'App\Listeners\ContentUpdatesGate',
		],

		'App\Events\Chat\PrivateMessageSent' => [
			'App\Listeners\UserNotificationsGate',
		],

		'App\Events\Mentioned' => [
			'App\Listeners\UserNotificationsGate',
		],

		// 'App\Events\QuizQuestionEdited' => [
		// 	'App\Listeners\UserNotificationsGate',
		// ],

		'App\Events\UserDataUpdated' => [
			'App\Listeners\BustUserCache',
		],

		'App\Events\Tasks\AssignedToTask' => [
			'App\Listeners\UserNotificationsGate',
		],

		'App\Events\Slides\SlideAdded' => [
			'App\Listeners\ContentUpdatesGate',
		],

		'App\Events\Slides\SlideRemoved' => [
			'App\Listeners\ContentUpdatesGate',
		],

		'App\Events\Slides\SlideUpdated' => [
			'App\Listeners\ContentUpdatesGate',
		],
	];

	/**
	 * Register any events for your application.
	 *
	 * @return void
	 */
	public function boot()
	{
		parent::boot();

		//
	}
}
