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

	public function searchByRoom(Request $request)
	{
		// I've decided to implement custom search method for this specific
		// scenario, in which we're wanting to query messages, but we're
		// actually granting access to chat room. Standard implementation would
		// require us to query and authorize the same resource.
		$roomName = $request->route('roomName');
		$room = ChatRoom::ofName($roomName)->first();
		$user = Auth::user();

		if (empty($room)) {
			return $this->respondNotFound();
		}

		if (!$user->can('view', $room)) {
			return $this->respondUnauthorized();
		}

		$messages = new ChatMessage;
		$messages = $messages->where('chat_room_id', $room->id);
		$messages = $this->applyFilters($messages, $request);

		return $this->transformAndRespond($messages->get());
	}

	public function getByMultipleRooms(Request $request)
	{
		$roomIds = $request->get('rooms');
		$messages = ChatMessage::select()
			->whereIn('chat_room_id', $roomIds)
			->orderBy('time', 'desc');

		return $this->transformAndRespond($messages);
	}
}
