<?php namespace App\Http\Controllers\Api\PrivateApi;

use App\Models\User;
use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\User\UpdateUserPassword;
use DB;

class UserPasswordApiController extends ApiController
{
	public function put(UpdateUserPassword $request)
	{
		$user = User::fetch($request->id);
		$hashedPassword = bcrypt($request->new_password);
		DB::table('users')->where('id', $user->id)->update(['password' => $hashedPassword]);
	}
}
