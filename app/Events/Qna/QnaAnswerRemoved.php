<?php

namespace App\Events\Qna;

use App\Events\Event;
use App\Events\SanitizesUserContent;
use App\Models\QnaAnswer;
use App\Traits\EventContextTrait;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;

class QnaAnswerRemoved extends Event
{
	use Dispatchable,
		InteractsWithSockets,
		SanitizesUserContent,
		EventContextTrait;

	public $model;

	public $userId;

	/**
	 * Create a new event instance.
	 *
	 * @param QnaAnswer $qnaAnswer
	 */
	public function __construct(QnaAnswer $qnaAnswer, $userId)
	{
		parent::__construct();
		$this->model = $qnaAnswer;
		$this->userId = $userId;
	}

	public function transform()
	{
		$this->data = [
			'event'   => 'qna-answer-deleted',
			'objects' => [
				'author' => $this->model->question->user->id,
				'type' => 'qna_question',
				'id'   => $this->model->question->id,
				'text' => $this->sanitize($this->model->question->text),
			],
			'subject' => [
				'type' => 'qna_answer',
				'id'   => $this->model->id,
				'text' => $this->sanitize($this->model->text),
			],
			'actors'  => [
				'id' => $this->userId
			],
			'referer' => $this->referer,
			'context' => $this->addEventContext($this->model)
		];
	}
}
