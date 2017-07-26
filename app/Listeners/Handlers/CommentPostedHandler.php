<?php namespace App\Listeners\Handlers;


use App\Events\CommentPosted;
use App\Listeners\UserNotificationsGate;

class CommentPostedHandler
{
	/**
	 * Notification rules for CommentPosted event.
	 *
	 * @param CommentPosted $event
	 * @param UserNotificationsGate $gate
	 */
	public function handle(CommentPosted $event, UserNotificationsGate $gate):void
	{
		$commentable = $event->comment->commentable;

		$gate->notifyModerators($event);

		$commentableAuthor = $commentable->user;

		if ($commentableAuthor) {
			$gate->notifyPrivate($commentableAuthor, $event);
		}

		$this->notifyCoCommentators($commentable, $gate, $event);
	}

	protected function notifyCoCommentators($commentable, $gate, $event):void
	{
		foreach ($commentable->comments as $comment) {
			$gate->notifyPrivate($comment->user, $event);
		}
	}
}
