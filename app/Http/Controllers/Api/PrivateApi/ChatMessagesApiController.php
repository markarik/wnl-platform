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
		$user = Auth::user();
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

		$roomsMessages = [];
		$limit = $request->limit ?? 10;
		$cursor = $request->currentCursor ?? null;

		foreach ($rooms as $room) {
			$messages = $this->eagerLoadIncludes(ChatMessage::class)
				->where('chat_room_id', $room->id)
				->orderBy('time', 'desc');

			$paginated = $this->cursorPaginatedResponse($messages, $cursor, $limit, 'time', '<');
			$roomsMessages[$room->id] = $paginated;
		}

		return $this->respondOk($roomsMessages);
	}

	public function getWithContext(Request $request) {
		$roomId = $request->roomId;
		$user = Auth::user();

		if (!$user->can('view', ChatRoom::find($roomId))) {
			return $this->respondForbidden();
		}

		$messageTime = $request->messageTime;
		$afterLimit = $request->afterLimit;
		$beforeLimit = $request->beforeLimit;

		$messagesAfterQuery = $this->eagerLoadIncludes(ChatMessage::class)
			->where('chat_room_id', $roomId)
			->orderBy('time', 'desc')
			->where('time', '>=', $messageTime);

		if (isset($afterLimit)) {
			$messagesAfterQuery->take($afterLimit);
		}

		$messagesAfter = $messagesAfterQuery->get();

		$messagesBeforeQuery = $this->eagerLoadIncludes(ChatMessage::class)
			->where('chat_room_id', $roomId)
			->orderBy('time', 'desc')
			->where('time', '<', $messageTime);

		if (isset($beforeLimit)) {
			$messagesBeforeQuery->take($beforeLimit);
		}

		$messagesBefore = $messagesBeforeQuery->get();

		$allMessages = $messagesAfter->concat($messagesBefore);
		$transformed = $this->transform($allMessages);
		$next = $allMessages->count() > 0 ? $allMessages->last()->time : null;
		$afterCount = $this->eagerLoadIncludes(ChatMessage::class)
			->where('chat_room_id', $roomId)
			->orderBy('time', 'desc')
			->where('time', '<', $messageTime)
			->count();

		return $this->respondOk([
			'data' => $transformed,
			'cursor' => [
				'current' => $messageTime,
				'next' => $next,
				'previous' => null,
				'has_more' => $afterCount > 0
			]
		]);
	}
}
