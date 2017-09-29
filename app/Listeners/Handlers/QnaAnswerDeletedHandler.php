<?php namespace App\Listeners\Handlers;

use App\Events\QnaAnswerDeleted;
use App\Listeners\UserNotificationsGate;
use App\Models\User;
use App\Models\QnaAnswer;

class QnaAnswerDeletedHandler
{
	/**
	 * Notification rules for QnaAnswerDeleted event.
	 *
	 * @param QnaAnswerDeleted $event
	 * @param UserNotificationsGate $gate
	 */
	public function handle(QnaAnswerDeleted $event, UserNotificationsGate $gate)
	{
		$answerAuthor = $event->qnaAnswer->user;
		$answerRemover = $event->data['actors']['id'] ?? 0;

		if ($answerAuthor->id !== $answerRemover) {
			$gate->notifyPrivate($answerAuthor, $event);
		}
	}
}
