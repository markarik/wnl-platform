<?php namespace App\Listeners\Handlers;

use App\Listeners\UserNotificationsGate;
use App\Events\QuizQuestionEdited;

class QuizQuestionEditedHandler
{
	/**
	 * Notification rules for Quiz Question edits
	 *
	 * @param AnswerPosted $event
	 * @param UserNotificationsGate $gate
	 */
	public function handle(QuizQuestionEdited $event, UserNotificationsGate $gate)
	{
		$watchers = $this->notifyWatchers($event->quizQuestion);

		if (count($watchers) === 0) {
			return;
		}

		forEach($watchers as $watcher) {
			$excluded->push($watcher);
		}

		$gate->notifyPrivateStream($watchers->pluck('id')->toArray(), $event);
	}

	protected function notifyWatchers($question)
	{
		$reaction = \App\Models\Reaction::type('watch');

		$reactables = \App\Models\Reactable::select()
			->where('reaction_id', $reaction->id)
			->where('reactable_type', 'App\\Models\\QuizQuestion')
			->where('reactable_id', $question->id)
			->get();

		$userIds = $reactables->pluck('user_id')->toArray();
		$users = \App\Models\User::whereIn('id', $userIds)
			->get();

		foreach ($users as $user) {
			$gate->notifyPrivate($user, $event);
		}

		return $users;
	}
}
