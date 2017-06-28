<?php

namespace App\Events\Qna;

use App\Models\QnaQuestion;
use Illuminate\Broadcasting\Channel;
use App\Events\SanitizesUserContent;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;

class QuestionPosted
{
	use Dispatchable,
		InteractsWithSockets,
		SerializesModels,
		SanitizesUserContent;

	public $qnaQuestion;

	const TEXT_LIMIT = 160;

	/**
	 * Create a new event instance.
	 *
	 * @param QnaQuestion $qnaQuestion
	 */
	public function __construct(QnaQuestion $qnaQuestion)
	{
		$this->qnaQuestion = $qnaQuestion;
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
			'event'   => 'qna-question-posted',
			'subject' => [
				'type' => 'qna_question',
				'id'   => $this->qnaQuestion->id,
				'text' => $this->sanitize($this->qnaQuestion->text),
			],
			'actors'  => [
				'id'         => $this->qnaQuestion->user->id,
				'first_name' => $this->qnaQuestion->user->first_name,
				'last_name'  => $this->qnaQuestion->user->last_name,
				'full_name'  => $this->qnaQuestion->user->full_name,
				'avatar'     => $this->qnaQuestion->user->profile->avatar,
			],
		];
	}
}
