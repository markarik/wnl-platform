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

		$limit = $request->limit ?? 10;
		$cursor = $request->currentCursor ?? null;

		$paginated = $this->cursorPaginatedResponse($messages, $cursor, $limit, 'time', '<');

		return $this->respondOk($paginated);
	}

	public function getWithContext(Request $request) {
		$roomId = $request->roomId;
		$messageTime = $request->messageTime;
		$afterLimit = $request->afterLimit;
		$beforeLimit = $request->beforeLimit;

		$messagesAfterQuery = ChatMessage::select()
			->where('chat_room_id', $roomId)
			->orderBy('time', 'desc')
			->where('time', '>=', $messageTime);

		if (isset($afterLimit)) {
			$messagesAfterQuery->take($afterLimit);
		}

		$messagesAfter = $messagesAfterQuery->get();

		$messagesBeforeQuery = ChatMessage::select()
			->where('chat_room_id', $roomId)
			->orderBy('time', 'desc')
			->where('time', '<', $messageTime);

		if (isset($beforeLimt)) {
			$messagesBeforeQuery->take($beforeLimit);
		}

		$messagesBefore = $messagesBeforeQuery->get();

		$allMessages = $messagesAfter->concat($messagesBefore);
		$transformed = $this->transform($allMessages);
		$next = $allMessages->count() > 0 ? $allMessages->last()->time : null;

		return $this->respondOk([
			'data' => $transformed,
			'cursor' => [
				'current' => $messageTime,
				'next' => $next,
				'previous' => null,
				'has_more' => true
			]
		]);
	}
}
