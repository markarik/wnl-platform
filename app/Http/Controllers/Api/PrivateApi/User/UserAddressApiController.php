<?php


namespace App\Http\Controllers\Api\PrivateApi\User;


use App\Http\Controllers\Api\ApiController;
use App\Http\Controllers\Api\Transformers\UserAddressTransformer;
use App\Http\Requests\User\UpdateUser;
use App\Models\User;
use League\Fractal\Resource\Item;

class UserAddressApiController extends ApiController
{
	public function get($id)
	{
		$user = User::fetch($id);
		$address = $user->address;

		if (!$user || !$address) {
			return $this->respondNotFound();
		}

		$resource = new Item($address, new UserAddressTransformer, 'user_address');
		$data = $this->fractal->createData($resource)->toArray();

		return $this->respondOk($data);
	}

	public function put(UpdateUser $request)
	{
		$user = User::fetch($request->route('id'));
		$user->address()->updateOrCreate(['user_id' => $user->id], $request->all());

		return $this->respondOk();
	}
}
