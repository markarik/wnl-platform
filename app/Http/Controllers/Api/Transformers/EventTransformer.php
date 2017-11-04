<?php

namespace App\Http\Controllers\Api\Transformers;


use App\Http\Controllers\Api\ApiTransformer;
use App\Models\Event;


class EventTransformer extends ApiTransformer
{
	protected $parent;

	public function __construct($parent = null)
	{
		$this->parent = $parent;
	}

	public function transform(Event $event)
	{
		$data = [
			'id'      => $event->id,
			'task_id' => $event->task_id,
			'tasks'   => $event->task_id,
			'data'    => $event->data,
		];

		if ($this->parent) {
			$data = array_merge($data, $this->parent);
		}

		return $data;
	}
}
