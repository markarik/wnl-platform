<?php namespace App\Http\Controllers\Api\PrivateApi\Events;

use App\Events\Mentioned;
use App\Http\Controllers\Api\ApiController;
use App\Models\User;
use Illuminate\Http\Request;

class MentionsApiController extends ApiController
{
	public function post(Request $request) {
		$originResource = $request->get('origin_resource');
		$originId = $request->get('origin_id');
		$mentions = $request->get('mentions');

		$originModel = self::getResourceModel($originResource);
		$origin = $originModel::find($originId);
		if (!$origin)
			return $this->respondInvalidInput('Origin resource doesn\'t exist.');

		foreach ($mentions as $userId) {
			$user = User::find($userId);
			event(new Mentioned($user, $origin));
		}

		return $this->respondOk();
	}
}
