<?php

namespace App\Events\Slides;

use App\Models\Slide;
use App\Traits\EventContextTrait;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SlideDetached
{
	use Dispatchable,
		InteractsWithSockets,
		SerializesModels,
		EventContextTrait;

	public $slide;

	public $presentables;

	/**
	 * Create a new event instance.
	 *
	 * @param Slide $slide
	 */
	public function __construct(Slide $slide, $presentables)
	{
		$this->slide = $slide;
		$this->presentables = $presentables;
	}

	/**
	 * Get the channels the event should broadcast on.
	 *
	 * @return \Illuminate\Broadcasting\Channel|array
	 */
	public function broadcastOn()
	{
		return new Channel('slides');
	}

	public function transform()
	{
		$this->data = [
			'event'        => 'slide-added',
			'subject'      => [
				'type' => 'slide',
				'id'   => $this->slide->id,
			],
			'presentables' => $this->presentables->pluck('id', 'type')->toArray(),
		];
	}
}
