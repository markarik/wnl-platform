<?php namespace App\Http\Controllers\Api\PrivateApi\User;

use Auth;
use App\Http\Controllers\Api\ApiController;
use Symfony\Component\HttpFoundation\Request;

class UserStateApiController extends ApiController
{
	public function get($id)
	{
		return $this->respondOk(["status"=>"all good"]);
	}

	public function patch(Request $request)
	{
		return $this->respondOk();
	}
}

