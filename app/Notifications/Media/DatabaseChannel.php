<?php

namespace App\Notifications\Media;

use App\Notifications\EventNotification;
use App\Models\Notification as NotificationModel;

class DatabaseChannel
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

		$channel = $notification->broadcastOn();

		NotificationModel::create([
			'id' => $notification->id,
			'type' => get_class($notification),
			'notifiable_id' => $notifiable->id,
			'notifiable_type' => get_class($notifiable),
			'data' => $notification->event->data,
			'event_id' => $notification->event->id,
			'channel' => $channel->name,
			'read_at' => null,
			'seen_at' => null,
		]);
	}
}
