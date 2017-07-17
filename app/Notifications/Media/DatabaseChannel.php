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
		NotificationModel::create([
			'id' => $notification->id,
			'type' => get_class($notification),
			'notifiable_id' => $notifiable->id,
			'notifiable_type' => get_class($notifiable),
			'data' => $notification->event->data,
			'channel' => $notification->channel,
			'read_at' => null,
			'seen_at' => null,
		]);
	}
}
