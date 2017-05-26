<?php

namespace App\Http\Controllers\Api\PrivateApi\Chat;

use Auth;
use App\Models\ChatRoom;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\ApiController;

class ChatMessagesApiController extends ApiController
{
	public function __construct(Request $request)
	{
		parent::__construct($request);
		$this->resourceName = config('papi.resources.chat-messages');
	}

	public function searchByRoom(Request $request)
	{
		// I've decided to implement custom search method for this specific scenario,
		// in which we're wanting to query messages, but we're actually granting access to chat room.
		// Standard implementation would require us to query and authorize the same resource.
		$roomName = $request->route('roomName');
		$room = ChatRoom::name($roomName);
		$user = Auth::user();

		if (!$room) {
			return $this->respondNotFound();
		}

		if ($user->can('view', $room)) {
			return $this->search($request);
		}

		return $this->respondUnauthorized();
	}
}
