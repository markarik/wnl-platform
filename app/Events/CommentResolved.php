<?php

namespace App\Events;

use App\Models\Comment;
use App\Traits\EventContextTrait;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Queue\SerializesModels;

class CommentResolved extends Event
{
	use Dispatchable,
		SerializesModels,
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
			'objects' => [
				'author' => $comment->user->id,
				'type' => 'comment',
				'id'   => $comment->id,
				'text' => $this->sanitize($comment->text ?? ''),
				'snippet' => $comment->snippet ?? [],
			],
			'subject' => [
				'type' => 'comment',
				'id'   => null,
				'text' => $this->sanitize($comment->text),
			],
			'commentable' => [
				'id' => $commentable->id,
				'type' => $commentableType
			],
			'actors'  => [
				'id' => $this->userId
			],
			'referer' => $this->referer,
			'context' => $this->addEventContext($commentable)
		];
	}
}
