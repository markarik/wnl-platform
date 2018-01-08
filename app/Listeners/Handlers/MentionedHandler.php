<?php namespace App\Listeners\Handlers;

use App\Events\Mentions\Mentioned;
use App\Listeners\UserNotificationsGate;
use App\Models\User;

class MentionedHandler
{
	/**
	 * Notification rules for Mentioned event.
	 *
	 * @param Mentioned $event
	 * @param UserNotificationsGate $gate
	 */
	public function handle(Mentioned $event, UserNotificationsGate $gate)
	{
		foreach ($event->mentioned as $userId) {
			$user = User::find($userId);
			$gate->notifyPrivate($user, $event);
		}
	}
}
