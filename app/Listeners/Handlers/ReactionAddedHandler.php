<?php namespace App\Listeners\Handlers;


use App\Events\ReactionAdded;
use App\Listeners\UserNotificationsGate;

class ReactionAddedHandler
{
	/**
	 * Notification rules for ReactionAdded event.
	 *
	 * @param ReactionAdded $event
	 * @param UserNotificationsGate $gate
	 */
	public function handle(ReactionAdded $event, UserNotificationsGate $gate)
	{
		$reactableAuthor = $event->reactable->user;

		if ($reactableAuthor && $event->reaction->type !== 'bookmark') {
			$gate->notifyPrivate($reactableAuthor, $event);
		}
	}
}
