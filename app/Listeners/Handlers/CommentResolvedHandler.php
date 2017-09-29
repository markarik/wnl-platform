<?php namespace App\Listeners\Handlers;


use App\Events\CommentResolved;
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
		$commentAuthor = $event->comment->user->id;
		$commentResolver = $event->data['actors']['id'] ?? 0;

		if ($commentAuthor !== $commentResolver) {
			$gate->notifyPrivate($commentAuthor, $event);
		}
	}
}
