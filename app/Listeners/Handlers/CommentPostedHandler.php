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
		$commentable = $event->comment->commentable;

		$gate->notifyModerators($event);

		$commentableAuthor = $commentable->user;

		if ($commentableAuthor) {
			$gate->notifyPrivate($commentableAuthor, $event);
		}

		$this->notifyCoCommentators($commentable, $gate, $event);
	}

	protected function notifyCoCommentators($commentable, $gate, $event)
	{
		$users = User::select()
			->whereHas('comments', function ($query) use ($commentable) {
				$query->whereIn('id', $commentable->comments->pluck('id'));
			})
			->where('id', '!=', $event->data['actor']['id'])
			->get();

		foreach ($users as $user) {
			$gate->notifyPrivate($user, $event);
		}
	}
}
