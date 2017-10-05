<?php namespace App\Listeners\Handlers\Common;


use App\Events\ResourceRestored;
use App\Listeners\UserNotificationsGate;
use App\Models\Notification;
use Illuminate\Support\Facades\Notification as Notify;
use App\Notifications\EventNotification;
use App\Notifications\Media\LiveChannel;

abstract class ResourceRestoredHandler
{
	abstract protected function shouldNotify($event);
	abstract protected function getUserToNotify($event);

	public function handle($event, UserNotificationsGate $gate)
	{
		if ($this->shouldNotify($event)) {
			$notification = $this->getNotification($event);

			$event->id = $notification->event_id;
			$event->data['deleted'] = true;

			$newNotification = new EventNotification($event, $notification->channel);
			$newNotification->id = $notification->id;
			$newNotification->event = $event;

			Notify::sendNow($this->getUserToNotify($event), $newNotification, [LiveChannel::class]);

			$notification->delete();
		}
	}

	private function getNotification($event) {
		return Notification::select()
		->where(function ($query) use ($event) {
			$query
				->whereRaw('data->"$.event" = "' . $event->data['event'] . '"')
				->whereRaw('data->"$.subject.id" = ' . $event->model->id)
				->whereRaw('data->"$.subject.type" = "' . $event->data['subject']['type'] . '"');
		})
		->first();
	}
}
