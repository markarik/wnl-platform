<?php namespace App\Listeners\Handlers;


use App\Events\CommentPosted;
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
		$comment = $event->comment;
		$commentable = $comment->commentable;

		$gate->notifyModerators($event);

		$commentableAuthor = $commentable->user;

		if ($commentableAuthor) {
			$gate->notifyPrivate($commentableAuthor, $event);
		}

		$excluded = $this->notifyCoCommentators($commentable, $gate, $event);

		$watchers = $this->notifyWatchers($comment, $gate, $event);

		forEach($watchers as $watcher) {
			$excluded->push($watcher);
		}

		$excluded->push($commentableAuthor);

		if ($commentable->comments->count() === 1){
			// Notify only about the first comment
			$gate->notifyPrivateStream($excluded->pluck('id')->toArray(), $event);
		}
	}

	protected function notifyCoCommentators($commentable, $gate, $event)
	{
		$query = User::select()
			->whereHas('comments', function ($query) use ($commentable) {
				$query->whereIn('id', $commentable->comments->pluck('id'));
			})
			->where('id', '!=', $event->data['actors']['id']);

		if (!empty($commentable->user)) {
			$query->where('id', '!=', $commentable->user->id);
		}

		$users = $query->get();

		foreach ($users as $user) {
			$gate->notifyPrivate($user, $event);
		}

		return $users;
	}

	protected function notifyWatchers($comment, $gate, $event)
	{
		$reaction = \App\Models\Reaction::type('watch');
		$reactables = \App\Models\Reactable::select()
			->where('reaction_id', $reaction->id)
			->where('reactable_type', $comment->commentable_type)
			->where('reactable_id', $comment->commentable_id)
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
