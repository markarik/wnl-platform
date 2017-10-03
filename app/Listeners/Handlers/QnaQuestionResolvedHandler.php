<?php namespace App\Listeners\Handlers;


use App\Events\QnaQuestionResolved;
use App\Listeners\UserNotificationsGate;
use App\Models\User;

class QnaQuestionResolvedHandler
{
	/**
	 * Notification rules for QnaQuestionResolved event.
	 *
	 * @param QnaQuestionResolved $event
	 * @param UserNotificationsGate $gate
	 */
	public function handle(QnaQuestionResolved $event, UserNotificationsGate $gate)
	{
		$questionAuthor = $event->qnaQuestion->user;
		$questionResolver = $event->data['actors']['id'] ?? 0;

		if ($questionAuthor->id !== $questionResolver) {
			$gate->notifyPrivate($questionAuthor, $event);
		}
	}
}
