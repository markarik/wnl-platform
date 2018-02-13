<?php namespace App\Http\Controllers\Api\PrivateApi;

use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\Chat\PostPrivateRoom;
use App\Models\ChatRoom;
use Auth;
use Illuminate\Http\Request;

class ChatRoomsApiController extends ApiController
{
	public function __construct(Request $request)
	{
		parent::__construct($request);
		$this->resourceName = config('papi.resources.chat-rooms');
	}

	public function getPrivateRooms()
	{
		$user = Auth::user();
		$select = [
			'chat_rooms.id',
			'chat_rooms.name',
			'max(chat_messages.time) as last_message_time',
		];

		$rooms = ChatRoom::with('users.profile')
			->select(\DB::raw(implode(',', $select)))
			->join(
				'chat_messages',
				'chat_messages.chat_room_id', '=', 'chat_rooms.id'
			)->join(
				'chat_room_user',
				'chat_room_user.chat_room_id', '=', 'chat_rooms.id'
			)
			->where('chat_room_user.user_id', $user->id)
			->orderBy('last_message_time', 'desc')
			->groupBy('chat_rooms.id');

		$data = $this->transform($rooms);

		return $this->respondOk($data);
	}

	public function createPrivateRoom(PostPrivateRoom $request)
	{
		$users = $request->users;
		$room = ChatRoom::where('name', $request->name)->first();

		if (
			!empty($room) &&
			$room->users->count() == count($users) &&
			$room->users->pluck('id')->diff($users)->count() == 0
		) {
			$data =  $this->transform($room);
			return $this->respondOk($data);
		} else {
			$room = ChatRoom::firstOrCreate(['name' => $request->name]);
			$room->users()->attach($request->users);

			$data = $this->transform($room);
			return $this->respondOk($data);
		}
	}
}
