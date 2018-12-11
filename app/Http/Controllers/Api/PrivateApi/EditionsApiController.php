<?php namespace App\Http\Controllers\Api\PrivateApi;

use App\Http\Controllers\Api\ApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class EditionsApiController extends ApiController
{
	const CACHE_VERSION = '1';
	const CACHE_KEY_PATTERN = 'editions-%s-%s';
	const CACHE_TTL = 60 * 24;

	public function __construct(Request $request)
	{
		parent::__construct($request);
		$this->resourceName = config('papi.resources.editions');
	}

	public function get($id)
	{
		$user = Auth::user();
		$key = self::key($user->id, __METHOD__);

		if(Cache::tags('editions')->has($key)) {
			return $this->respondOk(Cache::tags('editions')->get($key));
		}

		$data = parent::get($id)->getData();

		Cache::tags('editions')->put($key, $data, self::CACHE_TTL);

		return $this->respondOk($data);
	}

	protected static function key($userId) {
		return sprintf(self::CACHE_KEY_PATTERN, self::CACHE_VERSION, $userId);
	}

	public static function clearUserCache($userId) {
		\Cache::tags('editions')->forget(self::key($userId));
	}

	public static function clearCache() {
		\Cache::tags('editions')->flush();
	}
}
