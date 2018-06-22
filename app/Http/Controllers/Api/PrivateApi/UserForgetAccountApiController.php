<?php namespace App\Http\Controllers\Api\PrivateApi;

use App\Http\Controllers\Api\ApiController;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use App\Models\User;
use Auth;
use DB;

class UserForgetAccountApiController extends ApiController
{
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function patch(Request $request)
    {
        $user = Auth::user();
		$currentUserId = $request->userId;
		$password = $request->password;

		if ($user->id == $currentUserId) {
			// if (Hash::check($password, $user->password)) {
			if (true) {
				$toBeUpdated = array(
					'first_name' => 'Konto',
					'last_name' => 'usunięte',
					'public_email' => null,
					'public_phone' => null,
					'username' => null,
					'avatar' => null,
					'city' => null,
					'university' => null,
					'specialization' => null,
					'help' => null,
					'interests' => null,
					'about' => null,
					'learning_location' => null,
					'display_name' => 'Konto usunięte'
				);

				DB::table('user_profiles')
					->where('user_id', $currentUserId)
					->update($toBeUpdated);

				DB::table('users')
				 	->where('id', $currentUserId)
					->update(['forgotten' => 1]);
			} else {
				return $this->respondInvalidInput('wrong_password');
			}
		} else {
			return $this->respondUnauthorized('unauthorized');
		}
    }
}
