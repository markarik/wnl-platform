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

	public function get($id)
	{
		if (!Auth::user()->isAdmin()) {
			return $this->respondForbidden();
		}

		return parent::get($id);
	}

	public function put()
	{
		\Log::notice(">>>UsersApiController::put called, track caller and remove!");
		return $this->respondForbidden();
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
