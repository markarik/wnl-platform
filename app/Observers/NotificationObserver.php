<?php namespace App\Observers;


use App\Models\Notification;
use App\Notifications\EventNotification;
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
			$this->pushLive($notification);
		}

		$this->eventsOn($dispatcher);
	}

	public function recoverEvent($notification)
	{
		$event = new \stdClass();
		$event->data = $notification->data;
		$event->id = $notification->event_id;
		
		$notification = new EventNotification($event, $notification->channel);
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
