<?php namespace App\Listeners\Handlers;


use App\Events\Qna\QnaQuestionPosted;
use App\Listeners\UserNotificationsGate;

class QnaQuestionPostedHandler
{
	/**
	 * Notification rules for QnaQuestionPosted event.
	 *
	 * @param QnaQuestionPosted $event
	 * @param UserNotificationsGate $gate
	 */
	public function handle(QnaQuestionPosted $event, UserNotificationsGate $gate)
	{
		$gate->notifyModerators($event);
		$gate->notifyPrivateStream($excluded = [], $event);
	}
}
