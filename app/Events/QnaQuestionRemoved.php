<?php

namespace App\Events;

use App\Models\QnaQuestion;
use App\Traits\EventContextTrait;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use App\Events\SanitizesUserContent;
use App\Events\Event;

class QnaQuestionRemoved extends Event
{
	use Dispatchable,
		InteractsWithSockets,
		SanitizesUserContent,
		EventContextTrait;

	public $qnaQuestion;
	public $action;
	public $userId;

	/**
	 * Create a new event instance.
	 *
	 * @param QnaQuestion $qnaQuestion
	 * @param int $userId
	 */
	public function __construct(QnaQuestion $qnaQuestion, $userId, $action)
	{
		parent::__construct();
		$this->qnaQuestion = $qnaQuestion;
		$this->userId = $userId;
		$this->action = $action;
	}

	public function transform()
	{
		$qnaQuestion = $this->serializedModel();

		$this->data = [
			'event'   => 'qna-question-' . $this->action,
			'subject' => [
				'type' => 'qna_question',
				'id'   => $this->qnaQuestion->id,
				'text' => $this->sanitize($qnaQuestion->text),
			],
			'actors'  => [
				'id' => $this->userId,
			],
			'referer' => $this->referer,
			'context' => $this->addEventContext($qnaQuestion)
		];
	}

	private function serializedModel() {
		$serializedModel = QnaQuestion::find($this->qnaQuestion->id);

		return !empty($serializedModel) ? $serializedModel : $this->qnaQuestion;
	}
}
