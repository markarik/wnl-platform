<?php namespace App\Events;

use App\Models\User;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;

class Mentioned extends Event
{
    use Dispatchable,
		InteractsWithSockets,
		SerializesModels;

	/**
	 * Create a new event instance.
	 *
	 * @param User $user
	 * @param $requestData
	 */
    public function __construct(User $user, $requestData)
    {
    	parent::__construct();
    }

	public function transform()
	{

    }
}
