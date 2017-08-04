<?php namespace App\Listeners\Handlers;

use App\Events\Qna\AnswerPosted;
use App\Listeners\UserNotificationsGate;
use App\Models\User;
use App\Models\QnaAnswer;

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
		$answer = QnaAnswer::find($event->qnaAnswer->id);
		$gate->notifyPrivate($user, $event);

		$excluded = $this->notifyCollaborators($answer, $gate, $event);
		$excluded->push($user->id);

		$gate->notifyPrivateStream($excluded, $event);
	}

	protected function notifyCollaborators($answer, $gate, $event)
	{
		$users = User::select()
			->whereHas('qnaAnswers', function ($query) use ($answer) {
				$query->whereIn('id', $answer->question->answers->pluck('id'));
			})
			->where('id', '!=', $event->data['actors']['id'])
			->where('id', '!=', $answer->question->user->id)
			->get();

		foreach ($users as $user) {
			$gate->notifyPrivate($user, $event);
		}

		return $users;
	}
}
