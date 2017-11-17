<?php

namespace App\Notifications\Media;

use App\Models\Event;
use App\Models\Task;
use App\Models\QnaAnswer;
use App\Notifications\EventTaskNotification;
use Ramsey\Uuid\Uuid;
use Carbon\Carbon;


class DatabaseTaskChannel
{
	/**
	 * Send the given notification.
	 *
	 * @param  mixed $notifiable
	 * @param EventTaskNotification $notification
	 */
	public function send($notifiable, EventTaskNotification $notification)
	{
		if ($notifiable->id === $notification->event->data['actors']['id']) {
			return;
		}

		$event = $notification->event;
		$taskSubject = [
			'type' => $event->data['objects']['type'] ?? $event->data['subject']['type'],
			'id'   => $event->data['objects']['id'] ?? $event->data['subject']['id'],
		];

		if ($taskSubject['type'] === 'qna_answer' && !empty(QnaAnswer::find($taskSubject['id']))) {
			$taskSubject = [
				'type' => 'qna_question',
				'id' => QnaAnswer::find($taskSubject['id'])->question->id
			];
		}

		$task = Task::firstOrNew(
			[
				'subject_type' => $taskSubject['type'],
				'subject_id'   => $taskSubject['id'],
			],
			[
				'id'              => Uuid::uuid4()->toString(),
				'subject_type'    => $taskSubject['type'],
				'subject_id'      => $taskSubject['id'],
				'notifiable_id'   => $notifiable->id,
				'notifiable_type' => get_class($notifiable),
				'team'            => $notification->team,
				'text'            => $notification->text,
			]);

		if ($task->status == Task::STATUS_DONE) {
			$task->status = Task::STATUS_REOPEN;
		}

		$task->updated_at = Carbon::now();

		$task->save();

		Event::create([
			'id'      => $event->id,
			'data'    => $event->data,
			'task_id' => $task->id,
		]);
	}
}
