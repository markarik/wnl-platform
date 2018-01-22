<?php


namespace App\Observers;


use App\Events\Tasks\AssignedToTask;
use App\Models\Task;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Notifications\Notifiable;


class TaskObserver
{
	use DispatchesJobs, Notifiable;

	public function updated(Task $task)
	{
		if ($task->isDirty(['assignee_id']) && !empty($task->assignee_id)) {
			event(new AssignedToTask($task));
		}
	}
}
