<?php namespace App\Http\Controllers\Api\PrivateApi\User;

use App\Models\User;
use App\Models\UserQuizResults;
use App\Http\Controllers\Api\ApiController;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redis;

class UserStateApiController extends ApiController
{

	public function __construct(Request $request)
	{
		parent::__construct($request);
		$this->resourceName = config('papi.resources.user-state');
	}

	// courseId - userId - cacheVersion
	const KEY_COURSE_TEMPLATE = 'UserState:Course:%s:%s:%s';
	// courseId - lessonId - userId - cacheVersion
	const KEY_LESSON_TEMPLATE = 'UserState:Course:%s:%s:%s:%s';
	// quizSetId - userId - cacheVersion
	const KEY_QUIZ_TEMPLATE = 'UserState:Quiz:%s:%s:%s';
	// userId - cacheVersion
	const KEY_USER_TIME_TEMPLATE = 'UserState:Time:%s:%s';
	const CACHE_VERSION = 1;
	const INCREMENT_BY_MINUTES = 3;

	public function getCourse($id, $courseId)
	{
		$values = Redis::get(self::getCourseRedisKey($id, $courseId));

		if (!empty($values)) {
			$lessons = json_decode($values);
		} else {
			$lessons = [];
		}

		return $this->json([
			'lessons' => $lessons
		]);
	}

	public function putCourse(Request $request, $id, $courseId)
	{
		$lessons = $request->lessons;

		Redis::set(self::getCourseRedisKey($id, $courseId), json_encode($lessons));

		return $this->respondOk();
	}

	public function getLesson($id, $courseId, $lessonId)
	{
		$values = Redis::get(self::getLessonRedisKey($id, $courseId, $lessonId));

		if (!empty($values)) {
			$lesson = json_decode($values);
		} else {
			$lesson = [];
		}
		return $this->json([
			'lesson' => $lesson
		]);
	}

	public function putLesson(Request $request, $id, $courseId, $lessonId)
	{
		$lesson = $request->lesson;

		Redis::set(self::getLessonRedisKey($id, $courseId, $lessonId), json_encode($lesson));

		return $this->respondOk();
	}

	public function getQuiz($id, $quizId)
	{
		$values = Redis::get(self::getQuizRedisKey($id, $quizId));

		if (!empty($values)) {
			$quiz = json_decode($values);
		} else {
			$quiz = [];
		}
		return $this->json([
			'quiz' => $quiz
		]);
	}

	public function putQuiz(Request $request, $id, $quizId)
	{
		$quiz = $request->quiz;
		$recordedAnswers = $request->recordedAnswers;

		try {
			if (!empty($recordedAnswers)) {
				foreach ($recordedAnswers as $record) {
					UserQuizResults::updateOrCreate($record, $record);
				}
			}
		} catch
		(QueryException $e) {
			throw $e;
		} finally {
			Redis::set(self::getQuizRedisKey($id, $quizId), json_encode($quiz));
		}

		return $this->respondOk();
	}

	public
	function getTime($user)
	{
		$userInstance = User::find($user);

		if (!Auth::user()->can('view', $userInstance)) {
			return $this->respondForbidden();
		}

		$time = Redis::get(self::getUserTimeRedisKey($user));

		return $this->json([
			'time' => empty($time) ? 0 : $time
		]);
	}

	public
	function incrementTime(Request $request, $user)
	{
		$userInstance = User::find($user);
		if (!Auth::user()->can('view', $userInstance)) {
			return $this->respondForbidden();
		}


		$time = Redis::get(self::getUserTimeRedisKey($user));
		$incrementedTime = $time + self::INCREMENT_BY_MINUTES;
		Redis::set(self::getUserTimeRedisKey($user), $incrementedTime);

		return $this->json([
			'time' => $incrementedTime
		]);
	}

	static function getCourseRedisKey($userId, $courseId)
	{
		return sprintf(self::KEY_COURSE_TEMPLATE, $courseId, $userId, self::CACHE_VERSION);
	}

	static function getLessonRedisKey($userId, $courseId, $lessonId)
	{
		return sprintf(self::KEY_LESSON_TEMPLATE, $courseId, $lessonId, $userId, self::CACHE_VERSION);
	}

	static function getQuizRedisKey($userId, $quizId)
	{
		return sprintf(self::KEY_QUIZ_TEMPLATE, $quizId, $userId, self::CACHE_VERSION);
	}

	static function getUserTimeRedisKey($userId)
	{
		return sprintf(self::KEY_USER_TIME_TEMPLATE, $userId, self::CACHE_VERSION);
	}
}
