<?php

namespace App\Events;

use Request;
use App\Models\QuizQuestion;
use App\Traits\EventContextTrait;
use Illuminate\Broadcasting\Channel;
use App\Events\SanitizesUserContent;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;

class QuizQuestionEdited extends Event
{
	use Dispatchable,
		InteractsWithSockets,
		SerializesModels,
		SanitizesUserContent,
		EventContextTrait;

	public $quizQuestion;

	/**
	 * Create a new event instance.
	 *
	 * @param QnaQuestion $qnaQuestion
	 */
	public function __construct(QuizQuestion $quizQuestion)
	{
		parent::__construct();
		$this->quizQuestion = $quizQuestion;
	}

	/**
	 * Get the channels the event should broadcast on.
	 *
	 * @return Channel|array
	 */
	public function broadcastOn()
	{
		return new PrivateChannel('channel-name');
	}

	public function transform()
	{
		$this->data = [
			'event'   => 'quiz-question-posted',
			'subject' => [
				'type' => 'quiz_question',
				'id'   => $this->quizQuestion->id,
				'text' => $this->sanitize($this->quizQuestion->text),
			],
			'actors'  => [],
		];
	}
}
