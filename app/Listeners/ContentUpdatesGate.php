<?php

namespace App\Listeners;

use App\Events\Live\LiveContentUpdated;
use Illuminate\Contracts\Queue\ShouldQueue;

class ContentUpdatesGate implements ShouldQueue
{
	/**
	 * Create the event listener.
	 *
	 */
	public function __construct()
	{
		//
	}

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

		broadcast($liveEvent);
	}
}
