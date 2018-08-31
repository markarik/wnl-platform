<?php

namespace App\Listeners;

use App\Events\Live\LiveContentUpdated;

class ContentUpdatesGate
{
	/**
	 * Handle the event.
	 *
	 * @param  $event
	 *
	 * @return void
	 */
	public function handle($event)
	{
		$event->transform();
		$liveEvent = new LiveContentUpdated($event->data, $event->broadcastOn());

		broadcast($liveEvent)->toOthers();
	}
}
