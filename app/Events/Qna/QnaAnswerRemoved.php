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

	public $qnaAnswer;

	public $userId;

	/**
	 * Create a new event instance.
	 *
	 * @param QnaAnswer $qnaAnswer
	 */
	public function __construct(QnaAnswer $qnaAnswer, $userId)
	{
		parent::__construct();
		$this->qnaAnswer = $qnaAnswer;
		$this->userId = $userId;
	}

	public function transform()
	{
		$this->data = [
			'event'   => 'qna-answer-deleted',
			'objects' => [
				'author' => $this->qnaAnswer->question->user->id,
				'type' => 'qna_question',
				'id'   => $this->qnaAnswer->question->id,
				'text' => $this->sanitize($this->qnaAnswer->question->text),
			],
			'subject' => [
				'type' => 'qna_answer',
				'id'   => $this->qnaAnswer->id,
				'text' => $this->sanitize($this->qnaAnswer->text),
			],
			'actors'  => [
				'id' => $this->userId
			],
			'referer' => $this->referer,
			'context' => $this->addEventContext($this->qnaAnswer)
		];
	}
}
