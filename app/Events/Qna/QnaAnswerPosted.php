<?php

namespace App\Events\Qna;

use App\Events\Event;
use App\Events\SanitizesUserContent;
use App\Events\TransformsEventActor;
use App\Models\QnaAnswer;
use App\Traits\EventContextTrait;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;

class QnaAnswerPosted extends Event
{
	use Dispatchable,
		InteractsWithSockets,
		SanitizesUserContent,
		EventContextTrait,
		TransformsEventActor;

	public $model;

	/**
	 * Create a new event instance.
	 *
	 * @param QnaAnswer $qnaAnswer
	 */
	public function __construct(QnaAnswer $qnaAnswer)
	{
		parent::__construct();
		$this->model = $qnaAnswer;
	}

	public function transform()
	{
		$this->data = [
			'event'   => 'qna-answer-posted',
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
			'actors'  => $this->transformActor($this->model->user),
			'referer' => $this->referer,
			'context' => $this->addEventContext($this->model)
		];
	}
}
