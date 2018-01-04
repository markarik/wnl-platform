<?php namespace App\Observers;

use App\Models\ChatRoom;
use Illuminate\Foundation\Bus\DispatchesJobs;


class ChatRoomObserver
{
	use DispatchesJobs;


	public function created(ChatRoom $chatRoom)
	{
		if ($chatRoom->is_private) {
			$ids = explode('-', str_replace('private-', '', $chatRoom->name));
			$chatRoom->users()->attach($ids);
		}
	}
}
