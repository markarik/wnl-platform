<?php namespace App\Http\Controllers\Api\PrivateApi;

use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\Course\UpdateCourse;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class CoursesApiController extends ApiController
{
	const CACHE_VERSION = '1';
	const CACHE_KEY_PATTERN = 'courses-%s-%s';
	const CACHE_TTL = 60 * 24;

	public function __construct(Request $request)
	{
		parent::__construct($request);
		$this->resourceName = config('papi.resources.courses');
	}

	public function get($id)
	{
		$user = Auth::user();
		$key = self::key($user->id);

		if(Cache::tags('courses')->has($key)) {
			return $this->respondOk(Cache::tags('courses')->get($key));
		}

		$data = parent::get($id)->getData();

		Cache::tags('courses')->put($key, $data, self::CACHE_TTL);

		return $this->respondOk($data);
	}

	public function put(UpdateCourse $request, $id) {
		$course = Course::find($id);

		if (empty($course)) {
			return $this->respondNotFound();
		}

		DB::transaction(function() use ($course, $request) {
			$course->update($request->all());

			foreach ($course->groups as $group) {
				$group->order_number = array_search($group->id, $request->groups) + 1;
				$group->save();
			}
		});

		self::clearCache();

		return $this->transformAndRespond($course);
	}

	protected static function key($userId) {
		return sprintf(self::CACHE_KEY_PATTERN, self::CACHE_VERSION, $userId);
	}

	public static function clearUserCache($userId) {
		\Cache::tags('courses')->forget(self::key($userId));
	}

	public static function clearCache() {
		\Cache::tags('courses')->flush();
	}
}
