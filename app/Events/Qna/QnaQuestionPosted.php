<?php

namespace App\Events\Qna;

use App\Events\Event;
use App\Events\SanitizesUserContent;
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
		EventContextTrait;

	public $model;
	public $tags;

	const TEXT_LIMIT = 160;

	/**
	 * Create a new event instance.
	 *
	 * @param QnaQuestion $qnaQuestion
	 */
	public function __construct(QnaQuestion $qnaQuestion, $tags)
	{
		parent::__construct();
		$this->model = $qnaQuestion;
		$this->tags = $tags;
	}

	public function transform()
	{
		if (empty($this->model::query()->find($this->model->id))) {
			\Log::debug("REMOVING JOB FROM QUEUE.... MODEL DOES NOT EXIST");
			$this->delete();
		} else {
			\Log::debug("QNA QUESTION STILL EXISTS");
		}

		$this->data = [
			'event'   => 'qna-question-posted',
			'subject' => [
				'type' => 'qna_question',
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
			],
			'referer' => $this->referer,
			'context' => $this->addEventContext($this->model)
		];
	}
}
