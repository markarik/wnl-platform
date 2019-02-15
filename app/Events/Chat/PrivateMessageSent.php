<?php

namespace App\Events\Chat;

use App\Events\Event;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PrivateMessageSent extends Event
{
	use Dispatchable,
		InteractsWithSockets,
		SerializesModels;
}
