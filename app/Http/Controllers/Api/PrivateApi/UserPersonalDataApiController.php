<?php namespace App\Http\Controllers\Api\PrivateApi;

use DB;
use App\Models\User;
use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\User\PostUserPersonalData;

class UserPersonalDataApiController extends ApiController
{
    public function post(PostUserPersonalData $request)
    {
        $user = User::fetch($request->userId);

        if ($request->personal_identity_number) {
            dd('dotarł');
        }
    }
}
