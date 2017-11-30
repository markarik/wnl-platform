<?php

namespace App\Events;

use App\Models\Comment;
use App\Traits\EventContextTrait;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;

class CommentPosted extends Event
{
	use Dispatchable,
		InteractsWithSockets,
		SerializesModels,
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
			'event'   => 'comment-posted',
			'objects' => [
				'author' => $commentable->user->id ?? null,
				'type' => $commentableType,
				'id'   => $commentable->id,
				'text' => $this->sanitize($commentable->text ?? ''),
				'snippet' => $commentable->snippet ?? [],
			],
			'subject' => [
				'type' => 'comment',
				'id'   => $comment->id,
				'text' => $this->sanitize($comment->text),
			],
			'actors'  => [
				'id'           => $actor->id,
				'first_name'   => $actor->profile->first_name,
				'last_name'    => $actor->profile->last_name,
				'full_name'    => $actor->profile->full_name,
				'display_name' => $actor->profile->display_name,
				'avatar'       => $actor->profile->avatar_url,
			],
			'referer' => $this->referer,
			'context' => $this->addEventContext($commentable)
		];
	}
}
