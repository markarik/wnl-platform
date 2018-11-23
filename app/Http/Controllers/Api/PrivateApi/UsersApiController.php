<?php namespace App\Http\Controllers\Api\PrivateApi;

use Auth;
use App\Models\User;
use Illuminate\Http\Request;
use League\Fractal\Resource\Item;
use App\Http\Controllers\Api\ApiController;
use App\Http\Controllers\Api\Transformers\UserTransformer;

class UsersApiController extends ApiController
{
	public function __construct(Request $request)
	{
		parent::__construct($request);
		$this->resourceName = config('papi.resources.users');
	}

	public function get($id)
	{
		return parent::get($id);
	}

	public function put()
	{
		\Log::notice(">>>UsersApiController::put called, track caller and remove!");
		return $this->respondForbidden();
	}
}
