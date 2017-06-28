<?php

namespace App\Notifications;

use Request;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Notifications\Channels\LiveChannel;
use Illuminate\Notifications\Messages\BroadcastMessage;

class EventNotification extends Notification
{
	use Queueable;

	public $event;

	/**
	 * Create a new notification instance.
	 *
	 * @param $event
	 */
	public function __construct($event)
	{
		$event->data['timestamp'] = time();
		$this->event = $event;
	}

	/**
	 * Get the notification's delivery channels.
	 *
	 * @param  mixed $notifiable
	 * @return array
	 */
	public function via($notifiable)
	{
		return [LiveChannel::class, 'database'];
	}

	public function toLive()
	{
		return new BroadcastMessage($this->event->data);
	}
	
	public function toBroadcast($notifiable)
	{
		return new BroadcastMessage($this->event->data);
	}

	public function toDatabase($notifiable)
	{
		return $this->event->data;
	}
}
