<?php namespace App\Observers;


use App\Models\Notification;
use App\Models\User;
use App\Notifications\EventNotification;
use App\Notifications\Media\LiveChannel;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Support\Facades\Notification as Notify;


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
			$this->pushLive($twinNotification);
		}

		$this->eventsOn($dispatcher);
	}

	public function pushLive($notification)
	{
		$event = new \stdClass();
		$event->data = $notification->data;
		$event->id = $notification->event_id;
		$event->data['read_at'] = $notification->read_at->timestamp ?? null;
		$event->data['seen_at'] = $notification->seen_at->timestamp ?? null;

		$user = User::find($notification->user_id);

		$newNotification = new EventNotification($event, $notification->channel);
		$newNotification->id = $notification->id;
		Notify::sendNow($user, $newNotification, [LiveChannel::class]);
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
