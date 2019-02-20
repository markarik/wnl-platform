<?php namespace App\Listeners\Handlers;

use App\Events\Quiz\QuizQuestionEdited;
use App\Listeners\UserNotificationsGate;

class QuizQuestionEditedHandler
{
	/**
	 * Notification rules for Quiz Question edits
	 *
	 * @param QuizQuestionEdited $event
	 * @param UserNotificationsGate $gate
	 */
	public function handle(QuizQuestionEdited $event, UserNotificationsGate $gate)
	{
		$watchers = $this->notifyWatchers($event->model, $event, $gate);

		if (count($watchers) === 0) {
			return;
		}

		$gate->notifyPrivateStream($watchers->pluck('id')->toArray(), $event);
	}

	protected function notifyWatchers($question, $event, $gate)
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
