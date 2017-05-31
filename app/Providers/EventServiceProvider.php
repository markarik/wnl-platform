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
			'App\Listeners\NotifyUser',
		],

		'App\Events\Qna\AnswerPosted' => [
			'App\Listeners\NotifyUser',
		],

		'App\Events\CommentPosted' => [
			'App\Listeners\NotifyUser',
		],

		'App\Events\ReactionAdded' => [
			'App\Listeners\NotifyUser',
		],

		'App\Events\Chat\PrivateMessageSent' => [
			'App\Listeners\NotifyUser',
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
