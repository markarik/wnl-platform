<?php namespace App\Http\Controllers\Api\PrivateApi\Events;

use App\Models\User;
use App\Events\Mentioned;
use App\Http\Requests\PostMention;
use App\Http\Controllers\Api\ApiController;

class MentionsApiController extends ApiController
{
	public function post(PostMention $request)
	{
		$data = $request->all();
		$mentionedUsers = $request->get('mentioned_users');

		foreach ($mentionedUsers as $userId) {
			$user = User::find($userId);
			event(new Mentioned($user, $data));
		}

		return $this->respondOk();
	}
}
