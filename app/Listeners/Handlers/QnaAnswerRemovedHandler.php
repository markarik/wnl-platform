<?php namespace App\Listeners\Handlers;

use App\Events\QnaAnswerRemoved;
use App\Listeners\UserNotificationsGate;
use App\Models\User;
use App\Models\QnaAnswer;

class QnaAnswerRemovedHandler
{
	/**
	 * Notification rules for QnaAnswerRemoved event.
	 *
	 * @param QnaAnswerRemoved $event
	 * @param UserNotificationsGate $gate
	 */
	public function handle(QnaAnswerRemoved $event, UserNotificationsGate $gate)
	{
		$answerAuthor = $event->qnaAnswer->user;
		$answerRemover = $event->data['actors']['id'] ?? 0;

		if ($answerAuthor->id !== $answerRemover) {
			$gate->notifyPrivate($answerAuthor, $event);
		}
	}
}
