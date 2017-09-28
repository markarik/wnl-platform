<?php

namespace App\Providers;

use Illuminate\Support\Facades\Event;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
	/**
	 * The event listener mappings for the application.
	 *
	 * @var array
	 */
	protected $listen = [
		'App\Events\Qna\QuestionPosted' => [
			'App\Listeners\UserNotificationsGate',
		],

		'App\Events\Qna\AnswerPosted' => [
			'App\Listeners\UserNotificationsGate',
		],

		'App\Events\CommentPosted' => [
			'App\Listeners\UserNotificationsGate',
		],

		'App\Events\CommentResolved' => [
			'App\Listeners\UserNotificationsGate',
		],

		'App\Events\ReactionAdded' => [
			'App\Listeners\UserNotificationsGate',
		],

		'App\Events\Chat\PrivateMessageSent' => [
			'App\Listeners\UserNotificationsGate',
		],

		'App\Events\Mentioned' => [
			'App\Listeners\UserNotificationsGate',
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
