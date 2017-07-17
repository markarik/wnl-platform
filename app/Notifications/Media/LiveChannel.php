<?php

namespace App\Notifications\Channels;

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\BroadcastMessage;
use App\Events\LiveNotificationCreated;

class LiveChannel
{
	/**
	 * Send the given notification.
	 *
	 * @param  mixed $notifiable
	 * @param  \Illuminate\Notifications\Notification $notification
	 * @return void
	 */
	public function send($notifiable, Notification $notification)
	{
		// This is a custom implementation of broadcast channel, as
		// the laravel's built-in broadcast channel doesn't allow for
		// using 'toOthers' method (or at least I haven't found a way to do that).
		$message = $notification->toLive($notifiable);

		$event = new LiveNotificationCreated(
			$notifiable, $notification, is_array($message) ? $message : $message->data
		);
		$event->dontBroadcastToCurrentUser();

		if ($message instanceof BroadcastMessage) {
			$event->onConnection($message->connection)
				->onQueue($message->queue);
		}

		broadcast($event);
	}
}
