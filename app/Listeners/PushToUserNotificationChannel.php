<?php namespace App\Listeners;

use App\Events\Qna\QuestionPosted;
use Illuminate\Contracts\Queue\ShouldQueue;

class PushToUserNotificationChannel implements ShouldQueue
{
	/**
	 * Create the event listener.
	 *
	 * @return void
	 */
	public function __construct()
	{
		//
	}

	/**
	 * Handle the event.
	 *
	 * @param  QuestionPosted $event
	 * @return void
	 */
	public function handle($event)
	{
		//
	}
}
