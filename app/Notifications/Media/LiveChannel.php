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
			$notifiable, $notification, $message->data
		);

		// If actor is not set on event we don't want to send live notification to users
		// Instead, let's log such situation and fix - because it shouldn't happen
		if (empty($notification->event->data['actors']['id'])) {
			\Log::error('Event is missing an actor', $notification->event);
			return;
		}

		// We don't want to use broadcast()->toOthers because X-Socket-ID header is not always set
		// Instead, we can relay on actors->id field which is added to all events
		// and is already used in DatabaseChannel.
		if ($notifiable->id === $notification->event->data['actors']['id']){
			return;
		}

		broadcast($event);
	}
}
