<?php

namespace App\Notifications;

use App\Models\Lesson;
use App\Notifications\Media\DatabaseTaskChannel;
use Illuminate\Broadcasting\Channel;
use Illuminate\Bus\Queueable;
use App\Notifications\Media\LiveChannel;
use Illuminate\Notifications\Notification;

class EventTaskNotification extends Notification
{
	use Queueable;

	public $event;

	public $channel;

	public $read_at;

	public $team;

	public $text;

	/**
	 * Create a new notification instance.
	 *
	 * @param $event
	 * @param $channel
	 * @param $team
	 */
	public function __construct($event, $channel, $team)
	{
		$event->data['timestamp'] = time();
		$this->event = $event;
		$this->channel = $channel;
		$this->team = $team;
		$this->text = $this->getTaskDescription();
	}

	/**
	 * Get the notification's delivery channels.
	 *
	 * @param  mixed $notifiable
	 *
	 * @return array
	 */
	public function via($notifiable)
	{
		return [LiveChannel::class, DatabaseTaskChannel::class];
	}

	public function broadcastOn()
	{
		return new Channel($this->channel);
	}

	protected function getTaskDescription()
	{
		$description = '';
		$event = $this->event->data;
		$method = camel_case($event['event']) . 'Description';
		if (method_exists($this, $method)) {
			$description = $this->$method($event);
		}

		return $description;
	}

	protected function qnaQuestionPostedDescription($event)
	{
		$lessonId = $event['context']['params']['lessonId'];
		$lesson = Lesson::find($lessonId);

		return __('tasks.descriptions.qna_question', [
			'lessonName' => $lesson->name ?? '',
		]);
	}

	protected function qnaAnswerPostedDescription($event)
	{
		$lessonId = $event['context']['params']['lessonId'];
		$lesson = Lesson::find($lessonId);

		return __('tasks.descriptions.qna_answer', [
			'lessonName' => $lesson->name ?? '',
		]);
	}

	protected function commentPostedDescription($event)
	{
		$objectType = $event['objects']['type'];

		if ($objectType === 'slide') {
			$lessonId = $event['context']['params']['lessonId'];
			$slideNumber = $event['context']['params']['slide'];
			$lesson = Lesson::find($lessonId);

			return __('tasks.descriptions.slide_comment', [
				'lessonName'  => $lesson->name ?? '',
				'slideNumber' => $slideNumber,
			]);
		}

		if ($objectType === 'qna_answer') {
			$lessonId = $event['context']['params']['lessonId'];
			$lesson = Lesson::find($lessonId);

			return __('tasks.descriptions.qna_answer_comment', [
				'lessonName' => $lesson->name ?? '',
			]);
		}

		if ($objectType === 'quiz_question') {
			$questionId = $event['objects']['id'];

			return __('tasks.descriptions.quiz_question_comment', [
				'questionId' => $questionId,
			]);
		}

		return '';
	}
}
