<?php

namespace App\Events\Quiz;

use App\Events\Event;
use App\Events\SanitizesUserContent;
use App\Models\QuizQuestion;
use App\Models\User;
use App\Traits\EventContextTrait;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;

class QuizQuestionEdited extends Event
{
	use Dispatchable,
		InteractsWithSockets,
		SanitizesUserContent,
		EventContextTrait;

	public $model;
	public $user;

	/**
	 * Create a new event instance.
	 * @param QuizQuestion $quizQuestion
	 * @param User $user
	 *
	 */
	public function __construct(QuizQuestion $quizQuestion, User $user)
	{
		parent::__construct();
		$this->model = $quizQuestion;
		$this->user = $user;
	}

	public function transform()
	{
		$this->data = [
			'event'   => 'quiz-question-posted',
			'subject' => [
				'type' => 'quiz_question',
				'id'   => $this->model->id,
				'text' => $this->sanitize($this->model->text),
			],
			'actors'  => [
				'id' => $this->user->id
			],
		];
	}
}
