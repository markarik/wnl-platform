<?php

namespace App\Notifications\Media;

use App\Events\Live\LiveNotificationCreated;
use App\Notifications\EventNotification;
use Illuminate\Notifications\Messages\BroadcastMessage;

class LiveChannel
{
	/**
	 * Send the given notification.
	 *
	 * @param  mixed $notifiable
	 * @param EventNotification $notification
	 */
	public function send($notifiable, $notification)
	{
		$message = new BroadcastMessage($notification->event->data);

		$event = new LiveNotificationCreated(
			$notifiable, $notification, is_array($message) ? $message : $message->data
		);

		// We don't want to use broadcast()->toOthers because X-Socket-ID header is not always set
		// Instead, we can relay on actors->id field which is added to all events
		// and is already used in DatabaseChannel.
		if ($notifiable->id === $notification->event->data['actors']['id']){
			return;
		}

		broadcast($event);
	}
}
