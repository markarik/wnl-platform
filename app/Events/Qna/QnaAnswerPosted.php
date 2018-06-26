<?php

namespace App\Events\Qna;

use App\Events\Event;
use App\Events\SanitizesUserContent;
use App\Models\QnaAnswer;
use App\Traits\EventContextTrait;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;

class QnaAnswerPosted extends Event
{
	use Dispatchable,
		InteractsWithSockets,
		SanitizesUserContent,
		EventContextTrait;

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
			'actors'  => [
				'id'           => $this->model->user->id,
				'first_name'   => $this->model->user->profile->first_name,
				'last_name'    => $this->model->user->profile->last_name,
				'full_name'    => $this->model->user->profile->full_name,
				'display_name' => $this->model->user->profile->display_name,
				'avatar'       => $this->model->user->profile->avatar_url,
				'forgotten'    => $this->model->user->forgotten,
			],
			'referer' => $this->referer,
			'context' => $this->addEventContext($this->model)
		];
	}
}
