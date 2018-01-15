<?php

namespace App\Events\Live;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Notifications\Events\BroadcastNotificationCreated;

class LiveNotificationCreated extends BroadcastNotificationCreated
{
	use InteractsWithSockets;

	public $broadcastQueue = 'notifications';
}
