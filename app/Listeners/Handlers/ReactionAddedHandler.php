<?php namespace App\Listeners\Handlers;

use App\Events\Reactions\ReactionAdded;
use App\Listeners\UserNotificationsGate;
use DB;

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
		$type = $event->reaction->type;
		
		if ($type === 'bookmark' || $type === 'watch') return;

		$notifiable = $event->reactable->user;

		if (!$notifiable) return;

		if ($this->isDuplicated($event, $notifiable)) return;

		$gate->notifyPrivate($notifiable, $event);
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
