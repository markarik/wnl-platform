<?php


namespace App\Http\Controllers\Api\Transformers;


use App\Models\ChatMessage;
use App\Http\Controllers\Api\ApiTransformer;


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
			'id'      => $chatMessage->id,
			'content' => $chatMessage->content,
			'time'    => $chatMessage->time,
		];

		if ($this->parent) {
			$data = array_merge($data, $this->parent);
		}

		return $data;
	}

	public function includeProfiles(ChatMessage $chatMessage)
	{
		$profile = $chatMessage->user->profile;

		return $this->item(
			$profile,
			new UserProfileTransformer(['chat_messages' => $chatMessage->id]),
			'profiles'
		);
	}
}
