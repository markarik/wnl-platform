<?php namespace App\Listeners\Handlers;

use App\Events\Qna\AnswerPosted;
use App\Listeners\UserNotificationsGate;

class AnswerPostedHandler
{
	/**
	 * Notification rules for AnswerPosted event.
	 *
	 * @param AnswerPosted $event
	 * @param UserNotificationsGate $gate
	 */
	public function handle(AnswerPosted $event, UserNotificationsGate $gate)
	{
		$gate->notifyModerators($event);

		$user = $event->qnaAnswer->question->user;
		$gate->notifyPrivate($user, $event);
	}
}
