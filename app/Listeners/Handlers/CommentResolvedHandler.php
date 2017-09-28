<?php namespace App\Listeners\Handlers;


use App\Events\CommentResolved;
use App\Models\Comment;
use App\Listeners\UserNotificationsGate;
use App\Models\User;

class CommentResolvedHandler
{
	/**
	 * Notification rules for CommentResolved event.
	 *
	 * @param CommentResolved $event
	 * @param UserNotificationsGate $gate
	 */
	public function handle(CommentResolved $event, UserNotificationsGate $gate)
	{
		$comment = Comment::find($event->comment->id);

		if (empty($comment)) {
			return;
		}

		$commentAuthor = $event->comment->user;

		if ($commentAuthor) {
			$gate->notifyPrivate($commentAuthor, $event);
		}
	}
}
