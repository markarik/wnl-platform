<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use App\Notifications\Media\LiveChannel;
use Illuminate\Notifications\Notification;
use Illuminate\Broadcasting\PrivateChannel;
use App\Notifications\Media\DatabaseChannel;

class EventNotification extends Notification
{
	use Queueable;

	public $event;

	public $channel;

	/**
	 * Create a new notification instance.
	 *
	 * @param $event
	 * @param $channel
	 */
	public function __construct($event, $channel)
	{
		$event->data['timestamp'] = time();
		$this->event = $event;
		$this->channel = $channel;
	}

	/**
	 * Get the notification's delivery channels.
	 *
	 * @param  mixed $notifiable
	 *
	 * @return array
	 */
	public function via($notifiable)
	{
		return [LiveChannel::class, DatabaseChannel::class];
	}

	public function broadcastOn()
	{
		return new PrivateChannel($this->channel);
	}
}
