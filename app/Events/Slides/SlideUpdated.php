<?php

namespace App\Events\Slides;

use App\Models\Slide;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SlideUpdated implements ShouldBroadcast
{
	use Dispatchable, InteractsWithSockets, SerializesModels;

	public $slide;

	public $channels;

	/**
	 * Create a new event instance.
	 *
	 * @param Slide $slide
	 */
	public function __construct(Slide $slide)
	{
		$this->slide = $slide;
		$this->channels = collect();
	}

	/**
	 * Get the channels the event should broadcast on.
	 *
	 * @return \Illuminate\Broadcasting\Channel|array
	 */
	public function broadcastOn()
	{
		$this->channels->push(new Channel('slides'));

		return $this->channels->toArray();
	}

	public function transform()
	{
		$this->data = [
			'event'   => 'slide-updated',
			'subject' => [
				'type' => 'slide',
				'id'   => $this->slide->id,
			],
			'context' => $this->addEventContext($this->slide),
		];
	}
}
