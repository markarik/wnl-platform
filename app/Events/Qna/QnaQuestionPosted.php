<?php

namespace App\Events\Qna;

use App\Events\Event;
use App\Events\SanitizesUserContent;
use App\Events\TransformsEventActor;
use App\Models\QnaQuestion;
use App\Traits\EventContextTrait;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;

class QnaQuestionPosted extends Event
{
	use Dispatchable,
		InteractsWithSockets,
		InteractsWithQueue,
		SanitizesUserContent,
		EventContextTrait,
		TransformsEventActor;

	public $model;

	const TEXT_LIMIT = 160;

	/**
	 * Create a new event instance.
	 *
	 * @param QnaQuestion $qnaQuestion
	 */
	public function __construct(QnaQuestion $qnaQuestion)
	{
		parent::__construct();
		$this->model = $qnaQuestion;
	}

	public function transform()
	{
		$this->data = [
			'event'   => 'qna-question-posted',
			'subject' => [
				'type' => 'qna_question',
				'id'   => $this->model->id,
				'text' => $this->sanitize($this->model->text),
			],
			'actors'  => $this->transformActor($this->model->user),
			'referer' => $this->referer,
			'context' => $this->addEventContext($this->model)
		];
	}
}
