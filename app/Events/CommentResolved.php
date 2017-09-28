<?php

namespace App\Events;

use App\Models\Comment;
use App\Traits\EventContextTrait;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;

class CommentResolved extends Event
{
	use Dispatchable,
		InteractsWithSockets,
		SanitizesUserContent,
		EventContextTrait;

	public $comment;

	/**
	 * Create a new event instance.
	 *
	 * @param Comment $comment
	 */
	public function __construct(Comment $comment)
	{
		parent::__construct();
		$this->comment = $comment;
	}

	public function transform()
	{
		$comment = $this->comment;
		$actor = $comment->user;
		$commentable = $comment->commentable;
		$commentableType = snake_case(class_basename($commentable));

		$this->data = [
			'event'   => 'comment-resolved',
			'objects' => [
				'author' => $actor->id,
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
				'id' => null
			],
			'referer' => $this->referer,
			'context' => $this->addEventContext($commentable)
		];
	}
}
