<?php

namespace App\Notifications;

use App\Events\Event;
use App\Notifications\Media\DatabaseChannel;
use App\Notifications\Media\LiveChannel;
use Illuminate\Broadcasting\Channel;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class EventNotification extends Notification
{
	use Queueable;

	public $event;

	public $channel;

	public $read_at;

	/**
	 * Create a new notification instance.
	 *
	 * @param Event $event
	 * @param string $channel
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

	/**
	 * @return Channel
	 */
	public function broadcastOn()
	{
		return new Channel($this->channel);
	}
}
