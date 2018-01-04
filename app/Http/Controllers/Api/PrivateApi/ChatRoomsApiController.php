<?php namespace App\Http\Controllers\Api\PrivateApi;

use App\Http\Controllers\Api\ApiController;
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
		$rooms = ChatRoom::select()
			->where('name', 'like', "private%-{$user->id}-%");

		return $this->transformAndRespond($rooms);
	}
}
