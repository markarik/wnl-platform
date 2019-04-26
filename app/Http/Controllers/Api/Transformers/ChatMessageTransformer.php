<?php


namespace App\Http\Controllers\Api\Transformers;


use App\Http\Controllers\Api\ApiTransformer;
use App\Models\ChatMessage;


class ChatMessageTransformer extends ApiTransformer
{
	protected $parent;

	protected $availableIncludes = ['profiles'];

	public function __construct($parent = null)
	{
		$this->parent = $parent;
	}


	public function transform(ChatMessage $chatMessage)
	{
		$data = [
			'id'           => $chatMessage->id,
			'content'      => $chatMessage->content,
			'time'         => $chatMessage->time,
			'chat_room_id' => $chatMessage->chat_room_id,
			'user_id' => $chatMessage->user_id,
		];

		if ($this->parent) {
			$data = array_merge($data, $this->parent);
		}

		return $data;
	}

	public function includeProfiles(ChatMessage $chatMessage)
	{
		$profile = $chatMessage->profiles;

		return $this->item(
			$profile,
			new ProfileTransformer(['chat_messages' => $chatMessage->id]),
			'profiles'
		);
	}
}
