<?php namespace App\Http\Controllers\Api\PrivateApi;

use App\Http\Controllers\Api\ApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserSubscriptionApiController extends ApiController
{
	public function __construct(Request $request)
	{
		parent::__construct($request);
		$this->resourceName = config('papi.resources.user-subscription');
	}

	public function getSubscription()
	{
		$user = Auth::User();

		return $this->transformAndRespond($user->subscription);
	}
}
