<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\StoreUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UsersApiController extends ApiController
{
	public function __construct(Request $request)
	{
		parent::__construct($request);
		$this->resourceName = config('papi.resources.users');
	}

	public function getCurrentUser()
	{
		$user = Auth::user();

		return response()->json([
			'id'         => $user->id,
			'first_name' => $user->first_name,
			'last_name'  => $user->last_name,
			'full_name'  => $user->full_name,
		]);
	}

	public function put(StoreUser $request)
	{

	}
}
