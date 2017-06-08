<?php namespace App\Http\Controllers\Api\PrivateApi\User;

use Auth;
use App\Http\Controllers\Api\ApiController;
use Symfony\Component\HttpFoundation\Request;
use Illuminate\Support\Facades\Redis;

class UserStateApiController extends ApiController
{
	const KEY_TEMPLATE = 'user-state-%s-%s';
	const CACHE_VERSION = 1;

	public function get($id)
	{
		$keys = ['lessonId', 'status', 'route', 'courseId'];
		$values = Redis::hmget($this->getRedisKey($id), ['lessonId', 'status', 'route', 'courseId']);
		$returnValue = [];

		for($i = 0; $i < count($keys); $i++) {
			$returnValue[$keys[$i]] = $values[$i];
		};

		return $this->json($returnValue);
	}

	public function patch(Request $request, $id)
	{
		$lessonId = $request->lessonId;
		$route = $request->route;
		$status = $request->status;
		$courseId = $request->courseId;

		Redis::hmset($this->getRedisKey($id), [
			'lessonId' => $lessonId,
			'status' => $status,
			'route' => json_encode($route),
			'courseId' => $courseId
		]);

		return $this->respondOk();
	}

	private function getRedisKey($userId)
	{
		return sprintf(self::KEY_TEMPLATE, $userId, self::CACHE_VERSION);
	}
}

