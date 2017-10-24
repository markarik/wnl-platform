<?php

namespace App\Http\Controllers\Api\Transformers;


use App\Http\Controllers\Api\ApiTransformer;
use App\Models\Task;


class TaskTransformer extends ApiTransformer
{
	protected $parent;

	public function __construct($parent = null)
	{
		$this->parent = $parent;
	}

	public function transform(Task $tag)
	{
		$data = [
			'id'          => $tag->id,
			'creator_id'  => $tag->creator_id,
			'assignee_id' => $tag->assignee_id,
			'priority'    => $tag->priority,
			'order'       => $tag->order,
			'status'      => $tag->status,
			'text'        => $tag->text,
			'labels'      => $tag->labels,
			'context'     => $tag->context,
		];

		if ($this->parent) {
			$data = array_merge($data, $this->parent);
		}

		return $data;
	}
}
