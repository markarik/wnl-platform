<?php namespace App\Http\Controllers\Api\PrivateApi\Events;

use Auth;
use App\Models\User;
use App\Events\Mentioned;
use App\Http\Requests\PostMention;
use App\Http\Controllers\Api\ApiController;

class MentionsApiController extends ApiController
{
	public function post(PostMention $request)
	{
		$data = $request->all();
		$data['actor'] = Auth::user();
		event(new Mentioned($data));

		return $this->respondOk();
	}
}
