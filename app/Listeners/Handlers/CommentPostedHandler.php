<?php namespace App\Listeners\Handlers;


use App\Events\Comments\CommentPosted;
use App\Listeners\UserNotificationsGate;
use App\Models\User;

class CommentPostedHandler
{
	/**
	 * Notification rules for CommentPosted event.
	 *
	 * @param CommentPosted $event
	 * @param UserNotificationsGate $gate
	 */
	public function handle(CommentPosted $event, UserNotificationsGate $gate)
	{
		$comment = $event->model;
		$commentable = $comment->commentable;

		$gate->notifyModerators($event);

		$commentableAuthor = $commentable->user;

		if ($commentableAuthor) {
			$gate->notifyPrivate($commentableAuthor, $event);
		}

		$watchers = $this->getWatchers($comment);
		$coComentators = $this->getCoComentators($commentable, $event);

		$excluded = $watchers->merge($coComentators)->unique('id');

		foreach ($excluded as $user) {
			$gate->notifyPrivate($user, $event);
		}

		if ($commentableAuthor) {
			$excluded->push($commentableAuthor);
		}

		if ($commentable->comments->count() === 1) {
			// Notify only about the first comment
			$gate->notifyPrivateStream($excluded->pluck('id')->toArray(), $event);
		}
	}

	protected function getCoComentators($commentable, $event)
	{
		$query = User::select()
			->whereHas('comments', function ($query) use ($commentable) {
				$query->whereIn('id', $commentable->comments->pluck('id'));
			})
			->where('id', '!=', $event->data['actors']['id']);

		if (!empty($commentable->user)) {
			$query->where('id', '!=', $commentable->user->id);
		}

		return $query->get();
	}

	protected function getWatchers($comment)
	{
		$reaction = \App\Models\Reaction::type('watch');
		$reactables = \App\Models\Reactable::select()
			->where('reaction_id', $reaction->id)
			->where('reactable_type', $comment->commentable_type)
			->where('reactable_id', $comment->commentable_id)
			->get();

		$userIds = $reactables->pluck('user_id')->toArray();
		return User::whereIn('id', $userIds)->get();
	}
}
