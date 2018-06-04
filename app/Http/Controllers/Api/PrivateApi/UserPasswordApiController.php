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
		$hashedOldPassword = bcrypt($request->old_password);
		// dd($hashedOldPassword, $user->password);
		$hashedNewPassword = bcrypt($request->new_password);
		// dd($hashedOldPassword, $user->password, $request->old_password);
		if (Hash::check($request->old_password, $user->password)) {
			dd('dobre stare hasło');
			DB::table('users')->where('id', $user->id)->update(['password' => $hashedPassword]);
			return $this->respondOK();
		} else {
			// return respondInvalidInput('zuy inpud');
			dd('złe stare hasło');
		}
	}
}
