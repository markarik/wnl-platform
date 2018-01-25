<?php


namespace App\Observers;


use App\Events\Tasks\AssignedToTask;
use App\Models\Task;
use App\Notifications\TaskCreated;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\App;


class TaskObserver
{
	use DispatchesJobs, Notifiable;


	public function updated(Task $task)
	{
		if ($task->isDirty(['assignee_id']) && !empty($task->assignee_id)) {
			event(new AssignedToTask($task));
		}
	}

	public function created(Task $task)
	{
		$keywords = ['Pomoc techniczna'];

		if ($task->subject_type === 'qna_question' &&
			array_intersect($task->labels['tags'], $keywords)
		) {
			$this->notify(new TaskCreated($task));
		}
	}

	public function routeNotificationForSlack()
	{
		if (App::environment('production')) {
			return env('SLACK_HELP_URL');
		} else {
			return env('SLACK_TEST');
		}
	}
}
