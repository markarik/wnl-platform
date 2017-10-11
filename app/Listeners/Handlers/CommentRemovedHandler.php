<?php namespace App\Listeners\Handlers;


use App\Events\CommentRemoved;
use App\Listeners\UserNotificationsGate;
use App\Models\User;

class CommentRemovedHandler
{
	/**
	 * Notification rules for CommentRemoved event.
	 *
	 * @param CommentRemoved $event
	 * @param UserNotificationsGate $gate
	 */
	public function handle(CommentRemoved $event, UserNotificationsGate $gate)
	{
		$commentAuthor = $event->comment->user;
		$commentRemover = $event->data['actors']['id'] ?? 0;

		if ($commentAuthor->id !== $commentRemover) {
			$gate->notifyPrivate($commentAuthor, $event);
		}
	}
}
