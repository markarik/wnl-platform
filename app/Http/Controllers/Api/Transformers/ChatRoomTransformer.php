<?php


namespace App\Http\Controllers\Api\Transformers;


use App\Http\Controllers\Api\ApiTransformer;
use App\Models\ChatRoom;


class ChatRoomTransformer extends ApiTransformer
{
	protected $parent;

	protected $availableIncludes = ['profiles'];

	public function __construct($parent = null)
	{
		$this->parent = $parent;
	}

	public function transform(ChatRoom $chatRoom)
	{
		$data = [
			'id'                => $chatRoom->id,
			'channel'           => $chatRoom->name,
			'last_message_time' => $chatRoom->last_message_time ?? null,
		];

		if ($this->parent) {
			$data = array_merge($data, $this->parent);
		}

		return $data;
	}

	public function includeProfiles(ChatRoom $chatRoom)
	{
		$profiles = collect();
		foreach ($chatRoom->users as $user) {
			$profiles->push($user->profile);
		}

		return $this->collection(
			$profiles,
			new UserProfileTransformer(['chat_rooms' => $chatRoom->id]),
			'profiles'
		);
	}
}
