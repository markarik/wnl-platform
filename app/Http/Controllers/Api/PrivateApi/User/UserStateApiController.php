<?php namespace App\Http\Controllers\Api\PrivateApi\User;

use Auth;
use App\Http\Controllers\Api\ApiController;
use Symfony\Component\HttpFoundation\Request;
use Illuminate\Support\Facades\Redis;

class UserStateApiController extends ApiController
{
	// courseId - userId - cacheVersion
	const KEY_COURSE_TEMPLATE = 'user-state-%s-%s-%s';
	// courseId - lessonId - userId - cacheVersion
	const KEY_LESSON_TEMPLATE = 'user-state-%s-%s-%s-%s';
	const CACHE_VERSION = 1;

	public function getCourse($id, $courseId)
	{
		$values = Redis::get(self::getCourseRedisKey($id, $courseId));

		return $this->json(json_decode($values));
	}

	public function patchCourse(Request $request, $id, $courseId)
	{
		$lessons = $request->lessons;

		Redis::set(self::getCourseRedisKey($id, $courseId), json_encode($lessons));

		return $this->respondOk();
	}

	public function getLesson($id, $courseId, $lessonId)
	{
		$values = Redis::get(self::getLessonRedisKey($id, $courseId, $lessonId));

		return $this->json(json_decode($values));
	}

	public function patchLesson(Request $request, $id, $courseId, $lessonId)
	{
		$lesson = $request->lesson;

		Redis::set(self::getLessonRedisKey($id, $courseId, $lessonId), json_encode($lesson));

		return $this->respondOk();
	}

	static function getCourseRedisKey($userId, $courseId)
	{
		return sprintf(self::KEY_COURSE_TEMPLATE, $courseId, $userId, self::CACHE_VERSION);
	}

	static function getLessonRedisKey($userId, $courseId, $lessonId)
	{
		return sprintf(self::KEY_COURSE_TEMPLATE, $courseId, $lessonId, $userId, self::CACHE_VERSION);
	}
}

