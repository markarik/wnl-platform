<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use App\Notifications\Media\LiveChannel;
use Illuminate\Notifications\Notification;
use App\Notifications\Media\DatabaseChannel;
use Illuminate\Notifications\Messages\BroadcastMessage;

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
		return [/*LiveChannel::class, */DatabaseChannel::class];
	}

	public function toLive()
	{
		return new BroadcastMessage($this->event->data);
	}

	public function toDatabase($notifiable)
	{
		//
	}
}
