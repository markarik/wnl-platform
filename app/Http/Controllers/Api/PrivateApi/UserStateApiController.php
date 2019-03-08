<?php namespace App\Http\Controllers\Api\PrivateApi;

use App\Http\Controllers\Api\ApiController;
use App\Jobs\ArchiveCourseProgress;
use App\Models\Comment;
use App\Models\Lesson;
use App\Models\QnaAnswer;
use App\Models\QnaQuestion;
use App\Models\QuizQuestion;
use App\Models\User;
use App\Models\UserCourseProgress;
use App\Models\UserQuestionsBankState;
use App\Models\UserQuizResults;
use App\Models\UserTime;
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
			'lessons' => $lessons,
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
			'lesson' => $lesson,
		]);
	}

	public function putLesson(Request $request, $id, $courseId, $lessonId)
	{
		$lesson = $request->lesson;

		if (empty($lesson)) {
			\Log::error('Trying to save empty progress for lesson');
			// return OK because we want to make it transparent for a user.
			return $this->respondOk();
		}

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
			'time' => $incrementedTime,
		]);
	}

	public function saveQuizPosition(Request $request)
	{
		$key = $this->hashedFilters($request->filters);
		$userId = $request->route('id');
		$user = User::find($userId);

		if (empty($user)) {
			return $this->respondNotFound('user does not exist');
		}

		$value = $request->position;
		$state = UserQuestionsBankState::firstOrNew(
			['user_id' => $userId]
		);

		if (!Auth::user()->can('update', $state)) {
			return $this->respondForbidden();
		}

		$state->key = $key;
		$state->value = $value;
		$state->save();

		return $this->respondOk([
			'position' => $value
		]);
	}

	public function getQuizPosition(Request $request)
	{
		$key = $this->hashedFilters($request->filters);
		$userId = $request->route('id');

		$state = UserQuestionsBankState::firstOrNew(
			['user_id' => $userId, 'key' => $key]
		);

		if (!Auth::user()->can('view', $state)) {
			return $this->respondForbidden();
		}

		return $this->respondOk([
			'position' => $state->value
		]);
	}

	public function getStats(Request $request, $user)
	{
		$userObject = User::find($user);

		if (!$userObject) {
			return $this->respondNotFound();
		}

		$stats = self::countUserStats($userObject);

		return $this->json($stats);
	}

	static function countUserStats(User $user)
	{
		// Ay Ay Ay Profile Id not User Id
		$profileId = $user->profile->id;
		$userId = $user->id;

		$userTime = UserTime::where('user_id', $userId)->orderBy('created_at', 'desc')->first();
		$userCourseProgress = UserCourseProgress::where('user_id', $profileId)
			->whereNull('section_id')
			->whereNull('screen_id');
		$userComments = Comment::where('user_id', $userId)->count();
		$qnaQuestionsPosted = QnaQuestion::where('user_id', $userId)->count();
		$qnaAnswersPosted = QnaAnswer::where('user_id', $userId)->count();
		$quizQuestionsSolved = UserQuizResults::where('user_id', $userId)->groupBy('quiz_question_id')->get(['quiz_question_id'])->count();
		$numberOfQuizQuestions = QuizQuestion::count();
		$numberOfLessons = Lesson::count();
		$completedCount = (clone $userCourseProgress)
			->where('status', 'complete')
			->count();

		$startedCount = (clone $userCourseProgress)
			->whereIn('status', ['in-progress', 'complete'])
			->count();

		return [
			'time'           => [
				'minutes' => !empty($userTime) ? $userTime->time : 0,
			],
			'lessons'        => [
				'completed' => $completedCount,
				'started'   => $startedCount,
				'total'     => $numberOfLessons,
			],
			'social'         => [
				'comments'      => $userComments,
				'qna_questions' => $qnaQuestionsPosted,
				'qna_answers'   => $qnaAnswersPosted,
			],
			'quiz_questions' => [
				'solved' => $quizQuestionsSolved,
				'total'  => $numberOfQuizQuestions,
			],
		];
	}

	public function deleteCourse($userId, $courseId) {
		$user = \Auth::user();
		$profileId = $user->profile->id;
		$userCourseProgress = UserCourseProgress::where('user_id', $profileId)->first();

		if (!is_null($userCourseProgress) && !$user->can('delete', $userCourseProgress)) {
			return $this->respondForbidden();
		}

		$lessonsKeys = Lesson::all()->pluck('id')->map(function($item) use ($profileId, $courseId) {
			return self::getLessonRedisKey($profileId, $courseId, $item);
		});

		$lessonsKeys->each(function($lessonKey) {
			Redis::del($lessonKey);
		});

		$courseKey = self::getCourseRedisKey($profileId, $courseId);
		Redis::del($courseKey);

		$userCourseProgress = UserCourseProgress::where('user_id', $profileId)->get();

		dispatch_now(new ArchiveCourseProgress($user, $userCourseProgress));

		UserCourseProgress::where('user_id', $profileId)->delete();

		$this->respondOk();
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
