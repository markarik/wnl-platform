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
	public $presentable;
	public $channels;
	public $data;

	/**
	 * Create a new event instance.
	 *
	 * @param Slide $slide
	 */
	public function __construct(Slide $slide, $presentable)
	{
		$this->channels = collect();
		$this->slide = $slide;
		$this->presentable = $presentable;
	}

	/**
	 * Get the channels the event should broadcast on.
	 *
	 * @return \Illuminate\Broadcasting\Channel|array
	 */
	public function broadcastOn()
	{
		$this->channels->push(new Channel('slides'));
		$resource = Bethink::getTypeByClassInstance($this->presentable);

		$this->channels->push(
			new Channel("presentable-{$resource}-{$this->presentable->id}")
		);

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
			'presentables' => !empty($this->presentable->type) ? $this->presentable->pluck('id', 'type')->toArray() : ''
		];
	}
}
