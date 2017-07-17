<?php namespace App\Listeners\Handlers;


use App\Events\Qna\QuestionPosted;
use App\Listeners\UserNotificationsGate;

class QuestionPostedHandler
{
	/**
	 * Notification rules for QuestionPosted event.
	 *
	 * @param QuestionPosted $event
	 * @param UserNotificationsGate $gate
	 */
	public function handle(QuestionPosted $event, UserNotificationsGate $gate)
	{
		$gate->notifyModerators($event);
	}
}
