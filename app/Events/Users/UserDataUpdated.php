<?php

namespace App\Events\Users;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

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
