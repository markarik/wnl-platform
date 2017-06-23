<?php

namespace App\Events\Qna;

use App\Models\QnaAnswer;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class AnswerPosted
{
	use Dispatchable, InteractsWithSockets, SerializesModels;

	const TEXT_LIMIT = 160;

	public $qnaAnswer;

	/**
	 * Create a new event instance.
	 *
	 * @param QnaAnswer $qnaAnswer
	 */
	public function __construct(QnaAnswer $qnaAnswer)
	{
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
				'type' => 'qna_question',
				'id'   => $this->qnaAnswer->question->id,
				'text' => str_limit($this->qnaAnswer->question->text, self::TEXT_LIMIT),
			],
			'subject' => [
				'type' => 'qna_answer',
				'id'   => $this->qnaAnswer->id,
				'text' => str_limit($this->qnaAnswer->text, self::TEXT_LIMIT),
			],
			'actors'  => [
				'id'         => $this->qnaAnswer->user->id,
				'first_name' => $this->qnaAnswer->user->first_name,
				'last_name'  => $this->qnaAnswer->user->last_name,
				'full_name'  => $this->qnaAnswer->user->full_name,
				'avatar'     => $this->qnaAnswer->user->profile->avatar,
			],
		];
	}
}
