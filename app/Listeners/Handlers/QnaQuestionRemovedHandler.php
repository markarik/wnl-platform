<?php namespace App\Listeners\Handlers;


use App\Events\Qna\QnaQuestionRemoved;
use App\Listeners\UserNotificationsGate;

class QnaQuestionRemovedHandler
{
	/**
	 * Notification rules for QnaQuestionRemoved event.
	 *
	 * @param QnaQuestionRemoved $event
	 * @param UserNotificationsGate $gate
	 */
	public function handle(QnaQuestionRemoved $event, UserNotificationsGate $gate)
	{
		$questionAuthor = $event->model->user;
		$questionRemover = $event->data['actors']['id'] ?? 0;

		if ($questionAuthor->id !== $questionRemover) {
			$gate->notifyPrivate($questionAuthor, $event);
		}
	}
}
