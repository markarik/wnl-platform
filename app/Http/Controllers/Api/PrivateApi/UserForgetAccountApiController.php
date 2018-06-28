<?php namespace App\Http\Controllers\Api\PrivateApi;

use App\Http\Controllers\Api\ApiController;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Ramsey\Uuid\Uuid;
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
			if (Hash::check($password, $user->password)) {
				$userProfileUpdates = array(
					'first_name' => 'account',
					'last_name' => 'deleted',
					'public_email' => null,
					'public_phone' => null,
					'username' => null,
					'avatar' => 'avatars/account-deleted.png',
					'city' => null,
					'university' => null,
					'specialization' => null,
					'help' => null,
					'interests' => null,
					'about' => null,
					'learning_location' => null,
					'display_name' => null
				);

				$userUpdates = array(
					'forgotten' => 1,
					'consent_newsletter' => null,
					'email' => 'KontoUsunięte'.Uuid::uuid4()->toString().'@bethink.pl'
				);

				DB::table('user_profiles')
					->where('user_id', $currentUserId)
					->update($userProfileUpdates);

				DB::table('users')
				 	->where('id', $currentUserId)
					->update($userUpdates);

			} else {
				return $this->respondInvalidInput('wrong_password');
			}
		} else {
			return $this->respondUnauthorized('unauthorized');
		}
    }
}
