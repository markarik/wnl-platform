<?php namespace App\Http\Controllers\Api\PrivateApi\User;

use App\Models\UserQuizResults;
use Auth;
use App\Http\Controllers\Api\ApiController;
use Illuminate\Http\Request;
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
	const CACHE_VERSION = 1;

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

		if (!empty($recordedAnswers)) {
			UserQuizResults::insert($recordedAnswers);
		}

		Redis::set(self::getQuizRedisKey($id, $quizId), json_encode($quiz));

		return $this->respondOk();
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
}

