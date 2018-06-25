<?php namespace App\Listeners\Handlers;

use App\Events\Qna\QnaAnswerRemoved;
use App\Listeners\UserNotificationsGate;

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
		$answerAuthor = $event->model->user;
		$answerRemover = $event->data['actors']['id'] ?? 0;

		if ($answerAuthor->id !== $answerRemover) {
			$gate->notifyPrivate($answerAuthor, $event);
		}
	}
}
