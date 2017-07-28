<?php namespace App\Listeners\Handlers;

use DB;
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
		$notifiable = $event->reactable->user;

		if ($this->isDuplicated($event, $notifiable)) return;

		if ($notifiable && $event->reaction->type !== 'bookmark') {
			$gate->notifyPrivate($notifiable, $event);
		}
	}

	private function isDuplicated($event, $notifiable):bool
	{
		$search = DB::table('notifications')
			->where('notifiable_id', $notifiable->id)
			->whereRaw('data->"$.actors.id" = ' . $event->data['actors']['id'])
			->whereRaw('data->"$.objects.id" = ' . $event->data['objects']['id'])
			->whereRaw('data->"$.objects.type" = "' . $event->data['objects']['type'] . '"')
			->whereRaw('data->"$.subject.reaction_type" = "' . $event->data['subject']['reaction_type'] . '"')
			->get();

		if ($search->count() > 0) return true;

		return false;
	}
}
