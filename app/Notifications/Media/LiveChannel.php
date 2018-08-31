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

		broadcast($event)->toOthers();
	}
}
