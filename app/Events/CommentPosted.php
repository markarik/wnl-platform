<?php

namespace App\Events;

use Request;
use App\Models\Comment;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class CommentPosted
{
	use Dispatchable,
		InteractsWithSockets,
		SerializesModels,
		SanitizesUserContent;

	const TEXT_LIMIT = 160;

	public $comment;

	public $referer;

	/**
	 * Create a new event instance.
	 *
	 * @param Comment $comment
	 */
	public function __construct(Comment $comment)
	{
		$this->comment = $comment;
		$this->referer = Request::header('X-BETHINK-LOCATION');
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
		$comment = $this->comment;
		$actor = $comment->user;
		$commentableType = snake_case(class_basename($comment->commentable));

		$this->data = [
			'event'   => 'comment-posted',
			'objects' => [
				'type' => $commentableType,
				'id'   => $comment->commentable->id,
			],
			'subject' => [
				'type' => 'comment',
				'id'   => $comment->id,
				'text' => $this->sanitize($comment->text),
			],
			'actors'  => [
				'id'         => $actor->id,
				'first_name' => $actor->profile->first_name,
				'last_name'  => $actor->profile->last_name,
				'full_name'  => $actor->profile->full_name,
				'avatar'     => $actor->profile->avatar_url,
			],
			'referer' => $this->referer,
		];

		if ($commentableType === 'qna_answer') $this->addQnaAnswerContext();
	}

	public function addQnaAnswerContext()
	{
		$qnaAnswer = $this->comment->commentable;

		$this->data['context'] = [
			'screenId' => $qnaAnswer->question->screen->id,
			'lessonId' => $qnaAnswer->question->screen->lesson->id,
		];
	}
}
