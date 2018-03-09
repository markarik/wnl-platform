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
			->orderBy('time', 'desc');

		$limit = $request->limit ? $request->limit : 10;
		$cursor = $request->currentCursor ? $request->currentCursor : null;

		$paginated = $this->cursorPaginatedResponse($messages, $cursor, $limit, 'time', '<');

		return $this->json($paginated);
	}

	public function getWithContext(Request $request) {
		$roomId = $request->roomId;
		$messageTime = $request->messageTime;
		$messagesBeforeContext = $request->context ? $request->context : 10;

		$messagesAfter = ChatMessage::select()
			->where('chat_room_id', $roomId)
			->orderBy('time', 'desc')
			->where('time', '>=', $messageTime)->get();

		$messagesBefore = ChatMessage::select()
			->where('chat_room_id', $roomId)
			->orderBy('time', 'desc')
			->where('time', '<', $messageTime)->get();


		$allMessages = $messagesAfter->concat($messagesBefore);
		$transformed = $this->transform($messagesAfter->concat($messagesBefore));

		return $this->json([
			'data' => $transformed,
			'cursor' => [
				'current' => $messageTime,
				'next' => $allMessages->last()->time,
				'previous' => null,
				'has_more' => true
			]
		]);
	}
}
