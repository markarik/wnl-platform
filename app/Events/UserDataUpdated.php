<?php

namespace App\Events;

use Cache;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;

class UserDataUpdated
{
	use Dispatchable, SerializesModels;
	public $model;

	/**
	 * Create a new event instance.
	 *
	 */
	public function __construct($model)
	{
		$this->model = $model;
	}
}
