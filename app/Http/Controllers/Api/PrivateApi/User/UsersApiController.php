<?php

namespace App\Http\Controllers\Api\PrivateApi\User;

use App\Http\Controllers\Api\ApiController;
use App\Http\Controllers\Api\Transformers\UserTransformer;
use App\Http\Requests\User\UpdateUser;
use App\Models\User;
use Illuminate\Http\Request;
use League\Fractal\Resource\Item;

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
		$resource = new Item($user, new UserTransformer, $this->resourceName);
		$data = $this->fractal->createData($resource)->toArray();

		return $this->respondOk($data);
	}

	public function put(UpdateUser $request)
	{
		$user = User::fetch($request->route('id'));
		$user->update($request->all());

		return $this->respondOk();
	}
}
