<?php namespace App\Listeners;

use App\Notifications\EventNotification;
use Illuminate\Contracts\Queue\ShouldQueue;

class NotifyUser implements ShouldQueue
{
	/**
	 * Handle the event.
	 *
	 * @param $event
	 * @return void
	 */
	public function handle($event)
	{
		$this->{'handle' . class_basename($event)}($event);
	}

	private function handleAnswerPosted($event)
	{
		$user = $event->qnaAnswer->question->user;

		$event->data = [
			'event'   => 'qna-answer-posted',
			'objects' => [
				'type' => 'qna_question',
				'id'   => $event->qnaAnswer->question->id,
				'text' => $event->qnaAnswer->question->text,
			],
			'actors'  => [
				'id'         => $event->qnaAnswer->user->id,
				'first_name' => $event->qnaAnswer->user->first_name,
				'last_name'  => $event->qnaAnswer->user->last_name,
			],
		];

		$user->notify(new EventNotification($event));
	}
}
