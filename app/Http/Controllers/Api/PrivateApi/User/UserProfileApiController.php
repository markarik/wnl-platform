<?php


namespace App\Http\Controllers\Api\PrivateApi\User;


use App\Http\Controllers\Api\ApiController;
use App\Http\Controllers\Api\Transformers\UserProfileTransformer;
use App\Http\Requests\User\UpdateUserProfile;
use App\Models\User;
use League\Fractal\Resource\Item;

class UserProfileApiController extends ApiController
{
	public function get($id)
	{
		$user = User::fetch($id);
		$profile = $user->profile;

		if (!$user || !$profile) {
			return $this->respondNotFound();
		}

		$resource = new Item($profile, new UserProfileTransformer, 'user_profile');
		$data = $this->fractal->createData($resource)->toArray();

		return $this->respondOk($data);
	}

	public function put(UpdateUserProfile $request)
	{
		$user = User::fetch($request->route('id'));
		$user->profile()->updateOrCreate(['user_id' => $user->id], $request->all());

		return $this->respondOk();
	}
}