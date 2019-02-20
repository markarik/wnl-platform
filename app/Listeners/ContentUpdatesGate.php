<?php

namespace App\Listeners;

use App\Events\Event;
use App\Events\Live\LiveContentUpdated;

class ContentUpdatesGate
{
	/**
	 * Handle the event.
	 *
	 * @param Event $event
	 *
	 * @return void
	 */
	public function handle($event)
	{
		if (method_exists($event, 'transform')) {
			$event->transform();
		}

		$liveEvent = new LiveContentUpdated($event->data, $event->broadcastOn());

		broadcast($liveEvent)->toOthers();
	}
}
