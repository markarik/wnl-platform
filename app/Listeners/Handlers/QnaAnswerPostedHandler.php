<?php namespace App\Listeners\Handlers;

use App\Events\Qna\QnaAnswerPosted;
use App\Listeners\UserNotificationsGate;
use App\Models\QnaAnswer;
use App\Models\User;

class QnaAnswerPostedHandler
{
	/**
	 * Notification rules for QnaAnswerPosted event.
	 *
	 * @param QnaAnswerPosted $event
	 * @param UserNotificationsGate $gate
	 */
	public function handle(QnaAnswerPosted $event, UserNotificationsGate $gate)
	{
		$gate->notifyModerators($event);

		$user = $event->qnaAnswer->question->user;
		$answer = QnaAnswer::find($event->qnaAnswer->id);
		$gate->notifyPrivate($user, $event);

		$watchers = $this->getWatchers($answer);
		$collaborators = $this->getCollaborators($answer, $event);
		$excluded = $watchers->merge($collaborators)->unique('id');
		foreach ($excluded as $user) {
			$gate->notifyPrivate($user, $event);
		}

		$excluded->push($user);

		$gate->notifyPrivateStream($excluded->pluck('id')->toArray(), $event);
	}

	protected function getCollaborators($answer, $event)
	{
		return User::select()
			->whereHas('qnaAnswers', function ($query) use ($answer) {
				$query->whereIn('id', $answer->question->answers->pluck('id'));
			})
			->where('id', '!=', $event->data['actors']['id'])
			->where('id', '!=', $answer->question->user->id)
			->get();
	}


	protected function getWatchers($answer)
	{
		$reaction = \App\Models\Reaction::type('watch');

		$reactables = \App\Models\Reactable::select()
			->where('reaction_id', $reaction->id)
			->where('reactable_type', 'App\\Models\\QnaQuestion')
			->where('reactable_id', $answer->question->id)
			->get();

		$userIds = $reactables->pluck('user_id')->toArray();
		return User::whereIn('id', $userIds)->get();
	}
}
