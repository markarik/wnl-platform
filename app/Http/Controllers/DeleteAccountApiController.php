<?php

namespace App\Http\Controllers\Api\PrivateApi;

use App\Http\Controllers\Api\ApiController;
use Illuminate\Http\Request;
use Auth;

class DeleteAccountApiController extends Controller
{
    public function delete($userId)
	{
		dd($userId);
	}
}
