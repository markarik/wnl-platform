<?php namespace App\Http\Controllers\Api\PrivateApi;


use App\Models\User;
use Illuminate\Http\Request;
use League\Fractal\Resource\Item;
use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\User\UpdateUserProfile;
use App\Http\Controllers\Api\Transformers\UserProfileTransformer;

class UserProfileApiController extends ApiController
{
	public function __construct(Request $request)
	{
		parent::__construct($request);
		$this->resourceName = config('papi.resources.profiles');
	}

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
