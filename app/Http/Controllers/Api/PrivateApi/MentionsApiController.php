<?php namespace App\Http\Controllers\Api\PrivateApi;

use App\Events\Mentions\Mentioned;
use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\PostMention;
use Auth;

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
