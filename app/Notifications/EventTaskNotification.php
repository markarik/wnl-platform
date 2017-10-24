<?php

namespace App\Notifications;

use App\Notifications\Media\DatabaseTaskChannel;
use Illuminate\Broadcasting\Channel;
use Illuminate\Bus\Queueable;
use App\Notifications\Media\LiveChannel;
use Illuminate\Notifications\Notification;

class EventTaskNotification extends Notification
{
	use Queueable;

	public $event;

	public $channel;

	public $read_at;

	public $team;

	/**
	 * Create a new notification instance.
	 *
	 * @param $event
	 * @param $channel
	 * @param $team
	 */
	public function __construct($event, $channel, $team)
	{
		$event->data['timestamp'] = time();
		$this->event = $event;
		$this->channel = $channel;
		$this->team = $team;
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
		return [LiveChannel::class, DatabaseTaskChannel::class];
	}

	public function broadcastOn()
	{
		return new Channel($this->channel);
	}
}
