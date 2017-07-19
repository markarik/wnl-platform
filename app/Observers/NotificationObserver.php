<?php namespace App\Observers;


use App\Models\Notification;
use Illuminate\Foundation\Bus\DispatchesJobs;


class NotificationObserver
{
	use DispatchesJobs;


	public function updated(Notification $notification)
	{
		if (str_is('*moderator*', $notification->channel)) {
			$this->syncModeratorNotifications($notification);
		}
	}

	public function syncModeratorNotifications($notification)
	{
		$eventId = $notification->event_id;
		$modifiedFields = $notification->getDirty();

		$twinNotifications = Notification::select()
			->where('event_id', $eventId)
			->where('id', '!=' ,$notification->event_id)
			->where('channel', 'like', 'private-role.moderator%')
			->get();

		$dispatcher = $this->eventsOff();

		foreach ($twinNotifications as $twinNotification) {
			$twinNotification->update($modifiedFields);
		}

		$this->eventsOn($dispatcher);
	}

	private function eventsOff()
	{
		$dispatcher = Notification::getEventDispatcher();
		Notification::unsetEventDispatcher();

		return $dispatcher;
	}

	private function eventsOn($dispatcher)
	{
		Notification::setEventDispatcher($dispatcher);
	}
}
