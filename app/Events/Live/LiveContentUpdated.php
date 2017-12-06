<?php


namespace App\Events\Live;


use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Notifications\Events\BroadcastNotificationCreated;

class LiveContentUpdated extends BroadcastNotificationCreated
{
	use InteractsWithSockets;
}