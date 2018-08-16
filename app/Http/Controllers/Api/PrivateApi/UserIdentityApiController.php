<?php namespace App\Http\Controllers\Api\PrivateApi;

use DB;
use App\Models\User;
use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\User\UpdateUserIdentity;

class UserIdentityApiController extends ApiController
{
    public function post(UpdateUserIdentity $request)
    {
        $user = User::fetch($request->id);

        if ($request->personal_identity_number) {
            DB::table('users')
                ->where('id', $user->id)
                ->update([
                    'personal_identity_number' => ($request->personal_identity_number),
                    'identity_type' => ($request->identity_type)
                ]);
        }
    }
}
