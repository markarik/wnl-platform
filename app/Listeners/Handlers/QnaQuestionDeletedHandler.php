<?php namespace App\Listeners\Handlers;


use App\Events\QnaQuestionDeleted;
use App\Listeners\UserNotificationsGate;
use App\Models\User;

class QnaQuestionDeletedHandler
{
	/**
	 * Notification rules for QnaQuestionDeleted event.
	 *
	 * @param QnaQuestionDeleted $event
	 * @param UserNotificationsGate $gate
	 */
	public function handle(QnaQuestionDeleted $event, UserNotificationsGate $gate)
	{
		$questionAuthor = $event->qnaQuestion->user;
		$questionRemover = $event->data['actors']['id'] ?? 0;

		if ($questionAuthor->id !== $questionRemover) {
			$gate->notifyPrivate($questionAuthor, $event);
		}
	}
}
