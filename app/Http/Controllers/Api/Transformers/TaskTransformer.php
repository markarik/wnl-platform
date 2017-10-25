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

	public function transform(Task $task)
	{
		$data = [
			'id'          => $task->id,
			'creator_id'  => $task->creator_id,
			'assignee_id' => $task->assignee_id,
			'priority'    => $task->priority,
			'order'       => $task->order,
			'status'      => $task->status,
			'text'        => $task->text,
			'labels'      => $task->labels,
			'context'     => $task->context,
		];

		if ($this->parent) {
			$data = array_merge($data, $this->parent);
		}

		return $data;
	}
}
