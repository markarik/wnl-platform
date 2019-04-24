<?php

namespace App\Events\Reactions;

use App\Events\Event;
use App\Events\SanitizesUserContent;
use App\Events\TransformsEventActor;
use App\Models\Reaction;
use App\Models\User;
use App\Traits\EventContextTrait;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Events\Dispatchable;

class ReactionAdded extends Event
{
	use Dispatchable,
		InteractsWithSockets,
		SanitizesUserContent,
		EventContextTrait,
		TransformsEventActor;

	public $reaction;

	public $reactable;

	public $userId;

	/**
	 * Create a new event instance.
	 *
	 * @param Reaction $reaction
	 * @param Model $reactable
	 */
	public function __construct(Reaction $reaction, Model $reactable, $userId)
	{
		parent::__construct();
		$this->reaction = $reaction;
		$this->reactable = $reactable;
		$this->userId = $userId;
	}

	public function transform()
	{
		$reaction = $this->reaction;
		$reactable = $this->reactable;
		$actor = User::find($this->userId);

		$this->data = [
			'event'   => 'reaction-added',
			'objects' => [
				'author' => $reactable->user->id ?? null,
				'type' => snake_case(class_basename($reactable)),
				'id'   => $reactable->id,
				'text' => $this->sanitize($reactable->text ?? ''),
			],
			'subject' => [
				'type'          => 'reaction',
				'reaction_type' => $reaction->type,
				'reaction_id'   => $reaction->id,
			],
			'actors'  => $this->transformActor($actor),
			'referer' => $this->referer,
			'context' => $this->addEventContext($reactable)
		];

		// can we think of something better? :(
		// this is here so we won't refresh all comments list on page but only the one related with commentable
		if (get_class($reactable) === 'App\Models\Comment') {
			$this->data['commentable'] = [
				'type' => snake_case(class_basename($reactable->commentable)),
				'id' => $reactable->commentable->id
			];
		}
	}
}
