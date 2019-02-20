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
		'App\Events\Qna\QnaQuestionPosted' => [
			'App\Listeners\UserNotificationsGate',
			'App\Listeners\ContentUpdatesGate',
		],

		'App\Events\Qna\QnaQuestionRemoved' => [
			'App\Listeners\UserNotificationsGate',
			'App\Listeners\ContentUpdatesGate',
		],

		'App\Events\Qna\QnaQuestionRestoredEvent' => [
			'App\Listeners\UserNotificationsGate',
			'App\Listeners\ContentUpdatesGate',
		],

		'App\Events\Qna\QnaAnswerPosted' => [
			'App\Listeners\UserNotificationsGate',
			'App\Listeners\ContentUpdatesGate',
		],

		'App\Events\Qna\QnaAnswerRemoved' => [
			'App\Listeners\UserNotificationsGate',
			'App\Listeners\ContentUpdatesGate',
		],

		'App\Events\Comments\CommentPosted' => [
			'App\Listeners\UserNotificationsGate',
			'App\Listeners\ContentUpdatesGate',
		],

		'App\Events\Comments\CommentRemoved' => [
			'App\Listeners\UserNotificationsGate',
			'App\Listeners\ContentUpdatesGate',
		],

		'App\Events\Comments\CommentRestored' => [
			'App\Listeners\UserNotificationsGate',
			'App\Listeners\ContentUpdatesGate',
		],

		'App\Events\Reactions\ReactionAdded' => [
			'App\Listeners\UserNotificationsGate',
			'App\Listeners\ContentUpdatesGate',
		],

		'App\Events\Mentions\Mentioned' => [
			'App\Listeners\UserNotificationsGate',
		],

		'App\Events\Tasks\AssignedToTask' => [
			'App\Listeners\UserNotificationsGate',
		],

		'App\Events\Slides\SlideAdded' => [
			'App\Listeners\ContentUpdatesGate',
		],

		'App\Events\Slides\SlideDetached' => [
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
