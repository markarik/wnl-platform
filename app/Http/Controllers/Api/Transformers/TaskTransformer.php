<?php

namespace App\Http\Controllers\Api\Transformers;


use App\Http\Controllers\Api\ApiTransformer;
use App\Models\Task;


class TaskTransformer extends ApiTransformer
{
	protected $parent;
	protected $availableIncludes = ['events', 'assigneeProfiles'];

	public function __construct($parent = null)
	{
		$this->parent = $parent;
	}

	public function transform(Task $task)
	{
		$data = [
			'id'           => $task->id,
			'creator_id'   => $task->creator_id,
			'assignee_id'  => $task->assignee_id,
			'priority'     => $task->priority,
			'order'        => $task->order,
			'status'       => $task->status,
			'text'         => $task->text,
			'labels'       => $task->labels,
			'team'         => $task->team,
			'subject_id'   => $task->subject_id,
			'subject_type' => $task->subject_type,
			'created_at'   => $task->created_at->timestamp,
			'updated_at'   => $task->updated_at->timestamp,
		];

		if ($this->parent) {
			$data = array_merge($data, $this->parent);
		}

		return $data;
	}

	public function includeEvents(Task $task)
	{
		$events = $task->events->sortBy('created_at');

		return $this->collection($events, new EventTransformer, 'events');
	}

	public function includeAssigneeProfiles(Task $task)
	{
		if ($task->assigneeProfiles) {
			$profile = $task->assigneeProfiles;
			return $this->item($profile, new ProfileTransformer(['tasks' => $task->id]), 'assigneeProfiles');
		}
	}
}
