<?php

namespace App\Events;

use Request;
use App\Models\QnaQuestion;
use App\Traits\EventContextTrait;
use Illuminate\Broadcasting\Channel;
use App\Events\SanitizesUserContent;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;

class QnaQuestionPosted extends Event
{
	use Dispatchable,
		InteractsWithSockets,
		SerializesModels,
		SanitizesUserContent,
		EventContextTrait;

	public $qnaQuestion;
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
		$this->qnaQuestion = $qnaQuestion;
		$this->tags = $tags;
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
				'id'           => $this->qnaQuestion->user->id,
				'first_name'   => $this->qnaQuestion->user->profile->first_name,
				'last_name'    => $this->qnaQuestion->user->profile->last_name,
				'full_name'    => $this->qnaQuestion->user->profile->full_name,
				'display_name' => $this->qnaQuestion->user->profile->display_name,
				'avatar'       => $this->qnaQuestion->user->profile->avatar_url,
			],
			'referer' => $this->referer,
			'context' => $this->addEventContext($this->qnaQuestion)
		];
	}
}
