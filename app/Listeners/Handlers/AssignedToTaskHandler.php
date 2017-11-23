<?php namespace App\Listeners\Handlers;

use App\Events\Tasks\AssignedToTask;
use App\Listeners\UserNotificationsGate;

class AssignedToTaskHandler
{
	/**
	 * Notification rules for Mentioned event.
	 *
	 * @param AssignedToTask $event
	 * @param UserNotificationsGate $gate
	 */
	public function handle(AssignedToTask $event, UserNotificationsGate $gate)
	{
		$user = $event->task->assignee;
		if (!empty($user)) {
			$gate->notifyPrivate($user, $event);
		}
	}
}
