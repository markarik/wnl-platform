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
		$values = Redis::get($this->getRedisKey($id));

		return $this->json(json_decode($values));
	}

	public function patch(Request $request, $id)
	{
		$lessons = $request->lessons;

		Redis::set($this->getRedisKey($id), json_encode($lessons));

		return $this->respondOk();
	}

	private function getRedisKey($userId)
	{
		return sprintf(self::KEY_TEMPLATE, $userId, self::CACHE_VERSION);
	}
}

