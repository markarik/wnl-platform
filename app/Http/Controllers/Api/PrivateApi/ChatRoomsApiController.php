<?php namespace App\Http\Controllers\Api\PrivateApi;

use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\Chat\PostPrivateRoom;
use App\Http\Requests\Chat\PostPublicRoom;
use App\Models\ChatRoom;
use App\Models\ChatRoomUser;
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
			'max(chat_room_user.unread_count) as unread_count',
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
		$usersCount = count($users);

		$matchingRoom = ChatRoomUser::selectRaw('chat_room_id')
			->whereRaw("chat_room_id in (SELECT chat_room_id FROM chat_room_user GROUP BY chat_room_id HAVING COUNT(chat_room_id) = {$usersCount})")
			->whereIn('user_id', $users)
			->groupBy('chat_room_id')
			->havingRaw("COUNT(distinct user_id) = {$usersCount}")
			->first();

		if (!empty($matchingRoom)) {
			$data = $this->transform(ChatRoom::find($matchingRoom->chat_room_id));

			return $this->respondOk($data);
		} else {
			$room = ChatRoom::firstOrCreate(['name' => $request->name]);
			$room->users()->syncWithoutDetaching($request->users);

			$data = $this->transform($room);

			return $this->respondOk($data);
		}
	}

	public function createPublicRoom(Request $request)
	{
		$user = \Auth::user();
		$slug = $request->slug;
		$room = ChatRoom::firstOrCreate([
			'slug' => $slug,
			'type' => 'public',
		]);

		if (!$user->can('view', $room)) {
			return $this->respondUnauthorized();
		}

		$data = $this->transform($room);

		return $this->respondOk($data);
	}
}
