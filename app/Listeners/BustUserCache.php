<?php

namespace App\Listeners;

use Cache;

class BustUserCache
{
	/**
	 * Handle the event.
	 *
	 * @param  object $event
	 *
	 * @return void
	 */
	public function handle($event)
	{
		$id = $event->model->user_id;
		Cache::tags('user-' . $id)->flush();
	}
}
