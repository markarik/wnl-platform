<?php

namespace App\Events\Qna;

use App\Models\QnaQuestion;
use App\Traits\EventContextTrait;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Queue\SerializesModels;
use App\Events\SanitizesUserContent;
use App\Events\Event;

class QnaQuestionResolved extends Event
{
	use Dispatchable,
		SerializesModels,
		InteractsWithSockets,
		SanitizesUserContent,
		EventContextTrait;

	public $qnaQuestion;

	public $userId;

	/**
	 * Create a new event instance.
	 *
	 * @param QnaQuestion $qnaQuestion
	 * @param int $userId
	 */
	public function __construct(QnaQuestion $qnaQuestion, $userId)
	{
		parent::__construct();
		$this->qnaQuestion = $qnaQuestion;
		$this->userId = $userId;
	}

	public function transform()
	{
		$this->data = [
			'event'   => 'qna-question-resolved',
			'subject' => [
				'type' => 'qna_question',
				'id'   => $this->qnaQuestion->id,
				'text' => $this->sanitize($this->qnaQuestion->text),
			],
			'actors'  => [
				'id'         => $this->userId,
			],
			'referer' => $this->referer,
			'context' => $this->addEventContext($this->qnaQuestion)
		];
	}
}
