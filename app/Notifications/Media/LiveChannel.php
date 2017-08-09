<?php

namespace App\Notifications\Media;

use App\Events\LiveNotificationCreated;
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
	public function send($notifiable, EventNotification $notification)
	{
		if ($notifiable->id === $notification->event->data['actors']['id']){
			return;
		}

		$message = new BroadcastMessage($notification->event->data);

		$event = new LiveNotificationCreated(
			$notifiable, $notification, is_array($message) ? $message : $message->data
		);

		$event
			->onConnection($message->connection)
			->onQueue($message->queue);

		broadcast($event);
	}
}
