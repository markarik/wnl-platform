<?php namespace App\Http\Controllers\Api\PrivateApi;

use App\Http\Controllers\Api\ApiController;
use Illuminate\Http\Request;
use App\Models\User;
use Auth;

class UserDeleteAccountApiController extends ApiController
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
		dd($request->password);
        $user = Auth::user()->id;
		// dd($request->userId, $user);
		if ($user == $request->userId) {
			dd('user is authorized');
		} else {
			dd('user is not authorized');
		}
    }
}
