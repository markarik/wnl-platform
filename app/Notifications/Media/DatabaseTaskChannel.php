<?php

namespace App\Notifications\Media;

use App\Models\Task;
use App\Notifications\EventTaskNotification;


class DatabaseTaskChannel
{
	/**
	 * Send the given notification.
	 *
	 * @param  mixed $notifiable
	 * @param EventTaskNotification $notification
	 */
	public function send($notifiable, EventTaskNotification $notification)
	{
		if ($notifiable->id === $notification->event->data['actors']['id']) {
			return;
		}

		Task::create([
			'id'              => $notification->id,
			'notifiable_id'   => $notifiable->id,
			'notifiable_type' => get_class($notifiable),
			'context'         => $notification->event->data,
			'event_id'        => $notification->event->id,
			'team'            => $notification->team,
		]);
	}
}
