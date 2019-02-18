<?php

namespace App\Notifications;

use App\Events\Event;
use App\Models\Lesson;
use App\Notifications\Media\DatabaseTaskChannel;
use App\Notifications\Media\LiveChannel;
use Illuminate\Broadcasting\Channel;
use Illuminate\Bus\Queueable;
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
	 * @param Event $event
	 * @param string $channel
	 * @param string $team
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
		$route = $this->getRouteFromEvent($event);
		$lessonId = $route['params']['lessonId'] ?? null;
		if ($lessonId) {
			$lesson = Lesson::find($lessonId);

			return __('tasks.descriptions.qna_question', [
				'lessonName' => $lesson->name ?? '',
			]);
		}

		if ($route) {
			$context = __('tasks.descriptions.context.' . $route['name']);
			return __('tasks.descriptions.qna_question', ['lessonName' => $context]);
		};

		return '';
	}

	protected function qnaAnswerPostedDescription($event)
	{
		$route = $this->getRouteFromEvent($event);

		$lessonId = $route['params']['lessonId'] ?? null;
		if ($lessonId) {
			$lesson = Lesson::find($lessonId);

			return __('tasks.descriptions.qna_answer', [
				'lessonName' => $lesson->name ?? '',
			]);
		};

		$context = __('tasks.descriptions.context.' . $route['name']);
		if ($context) {
			return __('tasks.descriptions.qna_answer', ['lessonName' => $context]);
		}

		return '';
	}

	protected function commentPostedDescription($event)
	{
		$objectType = $event['objects']['type'];
		$route = $this->getRouteFromEvent($event);

		if ($objectType === 'slide') {
			$lessonId = $route['params']['lessonId'] ?? null;
			$slideNumber = $route['params']['slide'] ?? null;
			if (!$lessonId || !$slideNumber) return '';
			$lesson = Lesson::find($lessonId);

			return __('tasks.descriptions.slide_comment', [
				'lessonName'  => $lesson->name ?? '',
				'slideNumber' => $slideNumber,
			]);
		}

		if ($objectType === 'qna_answer') {
			$lessonId = $route['params']['lessonId'] ?? null;
			if (!$lessonId) {
				$lesson = Lesson::find($lessonId);

				return __('tasks.descriptions.qna_answer_comment', [
					'lessonName' => $lesson->name ?? '',
				]);
			};

			$context = __('tasks.descriptions.context.' . $route['name']);
			if ($context) {
				return __('tasks.descriptions.qna_answer_comment', ['lessonName' => $context]);
			}

			return '';
		}

		if ($objectType === 'quiz_question') {
			$questionId = $event['objects']['id'];

			return __('tasks.descriptions.quiz_question_comment', [
				'questionId' => $questionId,
			]);
		}

		return '';
	}

	protected function getRouteFromEvent($event) {
		if (!$event['context']) {
			return [];
		}

		if (isset($event['context']['route'])) {
			return $event['context']['route'];
		}

		return $event['context'];
	}
}
