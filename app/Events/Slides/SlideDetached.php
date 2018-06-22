<?php

namespace App\Events\Slides;

use App\Models\Slide;
use App\Traits\EventContextTrait;
use Facades\Lib\Bethink\Bethink;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;

class SlideDetached
{
	use Dispatchable,
		InteractsWithSockets,
		EventContextTrait;

	public $slide;

	public $presentables;

	public $channels;

	/**
	 * Create a new event instance.
	 *
	 * @param Slide $slide
	 */
	public function __construct(Slide $slide, $presentables)
	{
		$this->channels = collect();
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
			'event'        => 'slide-detached',
			'subject'      => [
				'type' => 'slide',
				'id'   => $this->slide->id,
			],
			'presentables' => $this->presentables->filter(function($presentable) {
				return $presentable->has('type');
			})->pluck('id', 'type')->toArray()
		];
	}
}
