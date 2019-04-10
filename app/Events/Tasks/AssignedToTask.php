<?php

namespace App\Events\Tasks;

use App\Events\Event;
use App\Models\Task;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AssignedToTask extends Event
{
	use Dispatchable,
		InteractsWithSockets,
		SerializesModels;

	public $task;

	public $actor;

	/**
	 * Create a new event instance.
	 *
	 * @param Task $task
	 */
	public function __construct(Task $task)
	{
		parent::__construct();
		$this->task = $task;
		$this->actor = clone \Auth::user();
	}

	public function transform()
	{
		$task = $this->task;

		$this->data = [
			'event'   => 'assigned-to-task',
			'objects' => [
				'type' => 'task',
				'id'   => $task->id,
				'text' => $task->text,
			],
			'subject' => [],
			'actors'  => [
				'id'           => $this->actor->id,
				'first_name'   => $this->actor->profile->first_name,
				'last_name'    => $this->actor->profile->last_name,
				'full_name'    => $this->actor->profile->full_name,
				'avatar'       => $this->actor->profile->avatar_url,
			],
			'referer' => $this->referer,
			'context' => []
		];
	}
}
