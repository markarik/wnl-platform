<?php


namespace App\Events\Live;


use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class LiveContentUpdated implements ShouldBroadcast
{
	use InteractsWithSockets;

	public $broadcastQueue = 'notifications';

	public $data;

	public $channels;

	public function __construct($data, $channels)
	{
		$this->data = $data;
		$this->channels = $channels;
	}

	public function broadcastOn()
	{
		return $this->channels;
	}
}
