<?php

namespace App\Events\Slides;

use App\Models\Presentable;
use App\Models\Slide;
use App\Traits\EventContextTrait;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SlideUpdated implements ShouldBroadcast
{
	use Dispatchable,
		InteractsWithSockets,
		SerializesModels,
		EventContextTrait;

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
		$presentables = Presentable::select()
			->where('slide_id', $this->slide->id)
			->get()
			->pluck('id', 'presentable_type')
			->toArray();

		$this->data = [
			'event'        => 'slide-updated',
			'subject'      => [
				'type' => 'slide',
				'id'   => $this->slide->id,
			],
			'context'      => $this->addEventContext($this->slide),
			'presentables' => $presentables,
		];
	}
}
