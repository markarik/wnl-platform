<?php namespace App\Http\Controllers\Api\PrivateApi;

use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\User\UpdateUser;
use App\Models\User;
use Auth;
use Illuminate\Http\Request;

class UsersApiController extends ApiController
{
	public function __construct(Request $request)
	{
		parent::__construct($request);
		$this->resourceName = config('papi.resources.users');
	}

	public function put()
	{
		\Log::notice(">>>UsersApiController::put called, track caller and remove!");
		return $this->respondForbidden();
	}

	public function forget(Request $request)
	{
		$user = Auth::user();
		$currentUserId = $request->userId;
		$password = $request->password;

		if ($user->id !== (int) $currentUserId) {
			return $this->respondForbidden('unauthorized');
		}

		if (!\Hash::check($password, $user->password)) {
			return $this->respondInvalidInput('wrong_password');
		}

		$user->forget();

		return $this->respondOk();
	}

	public function post(UpdateUser $request) {
		$user = User::create([
			'first_name' => $request->get('first_name'),
			'last_name' => $request->get('last_name'),
			'email' => $request->get('email'),
			'password' => bcrypt($request->get('passowrd'))
		]);

		$user->roles()->sync($request->get('roles'));

		return $this->respondOk();
	}
}
