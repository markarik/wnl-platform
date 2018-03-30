<?php

namespace App\Events\Qna;

use App\Events\Event;
use App\Events\SanitizesUserContent;
use App\Models\QnaAnswer;
use App\Traits\EventContextTrait;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class QnaAnswerPosted extends Event
{
	use Dispatchable,
		InteractsWithSockets,
		SerializesModels,
		SanitizesUserContent,
		EventContextTrait;

	public $qnaAnswer;

	/**
	 * Create a new event instance.
	 *
	 * @param QnaAnswer $qnaAnswer
	 */
	public function __construct(QnaAnswer $qnaAnswer)
	{
		parent::__construct();
		$this->qnaAnswer = $qnaAnswer;
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
			'event'   => 'qna-answer-posted',
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
				'id'           => $this->qnaAnswer->user->id,
				'first_name'   => $this->qnaAnswer->user->profile->first_name,
				'last_name'    => $this->qnaAnswer->user->profile->last_name,
				'full_name'    => $this->qnaAnswer->user->profile->full_name,
				'display_name' => $this->qnaAnswer->user->profile->display_name,
				'avatar'       => $this->qnaAnswer->user->profile->avatar_url,
			],
			'referer' => $this->referer,
			'context' => $this->addEventContext($this->qnaAnswer)
		];
	}
}
