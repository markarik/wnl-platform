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
		$user = User::fetch($id);

		if (!Auth::user()->can('view', $user)) {
			return $this->respondForbidden();
		}

		$resource = new Item($user, new UserTransformer, $this->resourceName);
		$data = $this->fractal->createData($resource)->toArray();

		return $this->respondOk($data);
	}

	public function put()
	{
		\Log::notice(">>>UsersApiController::put called, track caller and remove!");
		return $this->respondForbidden();
	}
}
