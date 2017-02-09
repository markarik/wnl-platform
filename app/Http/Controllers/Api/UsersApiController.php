<?php

namespace App\Http\Controllers\Api;

use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;

class UsersApiController extends Controller {

	public function getCurrentUser()
	{
		$user = Auth::user();

		return response()->json([
			'id' => $user->id,
			'first_name' => $user->first_name,
			'last_name' => $user->last_name,
		]);
	}
}
