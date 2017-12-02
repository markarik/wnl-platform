<?php

namespace App\Listeners;

use App\Events\Slides\SlideUpdated;
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
     * @param  SlideUpdated  $event
     * @return void
     */
    public function handle(SlideUpdated $event)
    {
        //
    }
}
