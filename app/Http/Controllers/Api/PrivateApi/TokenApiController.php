<?php namespace App\Http\Controllers\Api\PrivateApi;

use App\Http\Controllers\Api\ApiController;

class TokenApiController extends ApiController
{
	public function getToken()
	{
		return $this->respondOk(['token' => csrf_token()]);
	}
}
