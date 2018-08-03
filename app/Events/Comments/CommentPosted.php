<?php

namespace App\Events\Comments;

use App\Events\Event;
use App\Events\SanitizesUserContent;
use App\Models\Comment;
use App\Traits\EventContextTrait;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;

class CommentPosted extends Event
{
	use Dispatchable,
		InteractsWithSockets,
		SanitizesUserContent,
		EventContextTrait;

	public $model;

	public $channels;

	/**
	 * Create a new event instance.
	 *
	 * @param Comment $comment
	 */
	public function __construct(Comment $comment)
	{
		parent::__construct();
		$this->model = $comment;
		$this->channels = collect();
	}

	public function transform()
	{
		$comment = $this->model;
		$actor = $comment->user;
		$commentable = $comment->commentable;
		$commentableType = snake_case(class_basename($commentable));

		$this->data = [
			'event'   => 'comment-posted',
			'objects' => [
				'author'  => $commentable->user->id ?? null,
				'type'    => $commentableType,
				'id'      => $commentable->id,
				'text'    => $this->sanitize($commentable->text ?? ''),
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
			'context' => $this->addEventContext($commentable),
		];
	}

	public function broadcastOn()
	{
		$commentable = $this->model->commentable;
		$commentableType = snake_case(class_basename($commentable));
		$this->channels->push(new Channel('comments'));
		$this->channels->push(new Channel(
			"commentable-{$commentableType}-{$commentable->id}"
		));

		return $this->channels->toArray();
	}
}
