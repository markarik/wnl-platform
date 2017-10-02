<?php

namespace App\Events;

use App\Models\Comment;
use App\Traits\EventContextTrait;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;

class CommentRestored extends Event
{
	use Dispatchable,
		InteractsWithSockets,
		SanitizesUserContent,
		EventContextTrait;

	public $comment;

	public $userId;

	/**
	 * Create a new event instance.
	 *
	 * @param Comment $comment
	 * @param int $userId
	 */
	public function __construct(Comment $comment, $userId)
	{
		parent::__construct();
		$this->comment = $comment;
		$this->userId = $userId;
	}

	public function transform()
	{
		$comment = $this->comment;
		$commentable = $comment->commentable;
		$commentableType = snake_case(class_basename($commentable));

		$this->data = [
			'event'   => 'comment-resolved',
			'subject' => [
				'text' => '',
			],
			'commentable' => [
				'id' => $commentable->id,
				'type' => $commentableType
			],
			'actors'  => [
				'id' => $this->userId
			],
			'referer' => '',
			'context' => '',
			'deleted' => true
		];
	}
}
