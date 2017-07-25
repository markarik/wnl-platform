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
	public function handle(CommentPosted $event, UserNotificationsGate $gate)
	{
		$gate->notifyModerators($event);

		$commentableAuthor = $event->comment->commentable->user;

		if ($commentableAuthor) {
			$gate->notifyPrivate($commentableAuthor, $event);
		}
	}
}
