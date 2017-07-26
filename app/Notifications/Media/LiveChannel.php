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
		// This is a custom implementation of broadcast channel, as
		// the laravel's built-in broadcast channel doesn't allow for
		// using 'toOthers' method (or at least I haven't found a way to do that).
		$message = new BroadcastMessage($notification->event->data);

		$event = new LiveNotificationCreated(
			$notifiable, $notification, is_array($message) ? $message : $message->data
		);

		$event
			->dontBroadcastToCurrentUser()
			->onConnection($message->connection)
			->onQueue($message->queue);

		broadcast($event);
	}
}
