<?php namespace App\Listeners\Handlers;


use App\Events\CommentDeleted;
use App\Listeners\UserNotificationsGate;
use App\Models\User;

class CommentDeletedHandler
{
	/**
	 * Notification rules for CommentDeleted event.
	 *
	 * @param CommentResolved $event
	 * @param UserNotificationsGate $gate
	 */
	public function handle(CommentDeleted $event, UserNotificationsGate $gate)
	{
		$commentAuthor = $event->comment->user;
		$commentRemover = $event->data['actors']['id'] ?? 0;

		if ($commentAuthor->id !== $commentRemover) {
			$gate->notifyPrivate($commentAuthor, $event);
		}
	}
}
