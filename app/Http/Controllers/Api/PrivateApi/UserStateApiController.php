<?php namespace App\Http\Controllers\Api\PrivateApi;

use App\Models\User;
use App\Models\UserTime;
use App\Models\UserCourseProgress;
use App\Models\Comment;
use App\Models\Lesson;
use App\Models\QnaQuestion;
use App\Models\QnaAnswer;
use App\Models\UserQuizResults;
use App\Models\QuizQuestion;
use App\Http\Controllers\Api\ApiController;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redis;
use Cache;

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
	// userId - cacheVersion
	const KEY_USER_TIME_TEMPLATE = 'UserState:Time:%s:%s';
	const CACHE_VERSION = 1;
	const INCREMENT_BY_MINUTES = 10;

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

	public function incrementTime(Request $request, $user)
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

	public function saveQuizPosition(Request $request, $user) {
		$cacheKey = $this->hashedFilters($request->filters);
		$cacheTags = $this->getFiltersCacheTags(config('papi.resources.quiz-questions'), $user);
		Cache::tags($cacheTags)->put($cacheKey, $request->position, 60 * 24);

		return $this->json([
			'position' => $request->position
		]);
	}

	public function getQuizPosition(Request $request, $user) {
		$cacheKey = $this->hashedFilters($request->filters);
		$cacheTags = $this->getFiltersCacheTags(config('papi.resources.quiz-questions'), $user);
		$cachedPosition = Cache::tags($cacheTags)->get($cacheKey, $request->position);

		return $this->json([
			'position' => $cachedPosition
		]);
	}

	public function getStats(Request $request, $user) {
		$userTime = UserTime::where('user_id', $user)->first();
		$userCourseProgress = UserCourseProgress::where('user_id', $user)->get();
		$userComments = Comment::where('user_id', $user)->count();
		$qnaQuestionsPosted = QnaQuestion::where('user_id', $user)->count();
		$qnaAnswersPosted = QnaAnswer::where('user_id', $user)->count();
		$quizQuestionsSolved = UserQuizResults::where('user_id', $user)->groupBy('quiz_question_id')->get(['quiz_question_id'])->count();
		$numberOfQuizQuestions = QuizQuestion::count();
		$numberOfLessons = Lesson::count();

		$stats = [
			'time' => [
				'minutes' => !empty($userTime) ? $userTime->time : 0
			],
			'lessons' => [
				'completed' => 0,
				'started' => 0,
				'total' => $numberOfLessons
			],
			'social' => [
				'comments' => $userComments,
				'qna_questions' => $qnaQuestionsPosted,
				'qna_answers' => $qnaAnswersPosted
			],
			'quiz_questions' => [
				'solved' => $quizQuestionsSolved,
				'total' => $numberOfQuizQuestions
			]
		];

		if (!empty($userCourseProgress)) {
			$grouped = $userCourseProgress->groupBy('status');
			$completedCount = isset($grouped['complete']) ? count($grouped['complete']) : 0;
			$startedCount  = isset($grouped['in-progress']) ? count($grouped['in-progress']) : 0;

			$stats['lessons']['completed'] = $completedCount;
			$stats['lessons']['started'] = $startedCount;
		}

		return $this->json($stats);
	}

	static function getCourseRedisKey($userId, $courseId)
	{
		return sprintf(self::KEY_COURSE_TEMPLATE, $courseId, $userId, self::CACHE_VERSION);
	}

	static function getLessonRedisKey($userId, $courseId, $lessonId)
	{
		return sprintf(self::KEY_LESSON_TEMPLATE, $courseId, $lessonId, $userId, self::CACHE_VERSION);
	}

	static function getUserTimeRedisKey($userId)
	{
		return sprintf(self::KEY_USER_TIME_TEMPLATE, $userId, self::CACHE_VERSION);
	}
}
