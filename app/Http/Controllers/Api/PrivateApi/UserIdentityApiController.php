<?php namespace App\Http\Controllers\Api\PrivateApi;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\User\UpdateUserIdentity;

class UserIdentityApiController extends ApiController
{
    public function post(UpdateUserIdentity $request)
    {
        dd('dieeeee');
    }
}
