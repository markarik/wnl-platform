<?php namespace App\Http\Controllers\Api\PrivateApi;


use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\User\UpdateUserProfile;
use App\Models\User;
use Illuminate\Http\Request;

class UserProfilesApiController extends ApiController
{
	public function __construct(Request $request)
	{
		parent::__construct($request);
		$this->resourceName = config('papi.resources.profiles');
	}

	public function get($id)
	{
		$user = User::fetch($id);

		if (is_null($user) || is_null($user->profile)) {
			return $this->respondNotFound();
		}

		return $this->transformAndRespond($user->profile);
	}

	public function put(UpdateUserProfile $request)
	{
		$user = User::fetch($request->route('id'));
		$user->profile()->updateOrCreate(['user_id' => $user->id], $request->all());

		return $this->respondOk();
	}
}
