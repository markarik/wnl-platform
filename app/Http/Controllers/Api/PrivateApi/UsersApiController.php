<?php namespace App\Http\Controllers\Api\PrivateApi;

use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\User\PostUser;
use App\Http\Requests\User\UpdateUser;
use App\Models\User;
use Auth;
use Illuminate\Database\Query\Builder;
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
		$currentUser = Auth::user();
		$userId = $id === 'current' ? $currentUser->id : $id;

		/** @var Builder $builder */
		$builder = $this->eagerLoadIncludes(User::class);

		/** @var User $user */
		$user = $builder->find($userId);

		if (empty($user)) {
			return $this->respondNotFound();
		}

		if (!$currentUser->can('view', $user)) {
			return $this->respondForbidden();
		}

		return $this->transformAndRespond($user);
	}

	public function put(UpdateUser $request, $userId)
	{
		$user = User::find($userId);

		if (empty($user)) {
			return $this->respondNotFound();
		}

		$user->first_name = $request->get('first_name');
		$user->last_name = $request->get('last_name');
		$user->email = $request->get('email');

		if ($request->get('password')) {
			$user->password = bcrypt($request->get('password'));
		}

		$user->roles()->sync($request->get('roles'));

		$user->save();

		return $this->respondOk();
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

	public function post(PostUser $request) {
		$user = User::createWithProfileAndBilling([
			'first_name' => $request->get('first_name'),
			'last_name' => $request->get('last_name'),
			'email' => $request->get('email'),
			'password' => bcrypt($request->get('password'))
		]);

		$user->roles()->sync($request->get('roles'));

		return $this->respondOk();
	}
}
