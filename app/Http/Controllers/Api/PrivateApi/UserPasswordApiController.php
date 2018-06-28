<?php namespace App\Http\Controllers\Api\PrivateApi;

use App\Models\User;
use App\Http\Controllers\Api\ApiController;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\User\UpdateUserPassword;
use DB;

class UserPasswordApiController extends ApiController
{
	public function put(UpdateUserPassword $request)
	{
		$user = User::fetch($request->id);
		$oldPassword = $request->old_password;
		$newPassword = $request->new_password;
		if ($oldPassword === $newPassword) {
			return $this->respondInvalidInput('same_passwords');
		} else if (Hash::check($oldPassword, $user->password)) {
			DB::table('users')
				->where('id', $user->id)
				->update(['password' => bcrypt($request->new_password)]);
			return $this->respondOK();
		} else {
			return $this->respondInvalidInput('wrong_old_password');
		}
	}
}
