<?php namespace App\Http\Controllers\Api\PrivateApi;

use App\Http\Controllers\Api\ApiController;
use App\Models\ChatMessage;
use App\Models\ChatRoom;
use Auth;
use Illuminate\Http\Request;

class ChatMessagesApiController extends ApiController
{
	public function __construct(Request $request)
	{
		parent::__construct($request);
		$this->resourceName = config('papi.resources.chat-messages');
	}

	public function getByMultipleRooms(Request $request)
	{
		$user = \Auth::user();
		$roomIds = $request->get('rooms');

		$rooms = ChatRoom::with('users')->whereIn('id', $roomIds)->get();

		if ($rooms->count() === 0) {
			return $this->respondNotFound();
		}

		foreach ($rooms as $room) {
			if (!$user->can('view', $room)) {
				return $this->respondForbidden();
			}
		}

		$messages = ChatMessage::select()
			->whereIn('chat_room_id', $roomIds)
			->orderBy('time', 'asc');

		return $this->transformAndRespond($messages);
	}
}
