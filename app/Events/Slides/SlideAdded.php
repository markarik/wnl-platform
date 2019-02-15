<?php

namespace App\Events\Slides;

use App\Models\Slide;
use App\Traits\EventContextTrait;
use Facades\Lib\Bethink\Bethink;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SlideAdded
{
	use Dispatchable,
		InteractsWithSockets,
		SerializesModels,
		EventContextTrait;

	public $slide;
	public $presentables;
	public $channels;
	public $data;

	/**
	 * Create a new event instance.
	 *
	 * @param Slide $slide
	 * @param $presentables
	 */
	public function __construct(Slide $slide, $presentables)
	{
		$this->slide = $slide;
		$this->presentables = $presentables;
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
		foreach ($this->presentables as $presentable) {
			$resource = Bethink::getTypeByClassInstance($presentable);

			$this->channels->push(
				new Channel("presentable-{$resource}-{$presentable->id}")
			);
		}

		return $this->channels->toArray();
	}

	public function transform()
	{
		$this->data = [
			'event'        => 'slide-added',
			'subject'      => [
				'type' => 'slide',
				'id'   => $this->slide->id,
			],
			'context'      => $this->addEventContext($this->slide),
			'presentables' => $this->presentables->pluck('id', 'type')->toArray(),
		];
	}
}
