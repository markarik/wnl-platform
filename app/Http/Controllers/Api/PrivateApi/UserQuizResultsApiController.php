<?php namespace App\Http\Controllers\Api\PrivateApi;

use App\Http\Controllers\Api\ApiController;
use App\Http\Controllers\Api\Transformers\UserQuizResultsTransformer;
use App\Jobs\CalculateExamResults;
use App\Models\Course;
use App\Models\QuizAnswer;
use App\Models\QuizQuestion;
use App\Models\User;
use App\Models\UserPlanProgress;
use App\Models\UserQuizResults;
use Auth;
use Carbon\Carbon;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use League\Fractal\Resource\Collection;
use Facades\App\Contracts\CourseProvider;


class UserQuizResultsApiController extends ApiController
{
	use DispatchesJobs;

	// quizSetId - userId - cacheVersion
	const KEY_QUIZ_TEMPLATE = 'UserState:Quiz:%s:%s:%s';
	const CACHE_VERSION = 1;
	const EXAM_FILTER = 'by_taxonomy-exams';

	public function __construct(Request $request)
	{
		parent::__construct($request);
		$this->resourceName = config('papi.resources.user-quiz-results');
	}

	public function get($userId)
	{
		$user = User::fetch($userId);

		if (!Auth::user()->can('view', $user)) {
			return $this->respondForbidden();
		}

		$resource = new Collection(UserQuizResults::where('user_id', $userId)->get(), new UserQuizResultsTransformer, $this->resourceName);
		$data = $this->fractal->createData($resource)->toArray();

		return $this->respondOk($data);
	}

	public function post(Request $request) {
		$results = $request->get('results');
		$userId = $request->route('userId');
		$user = User::fetch($userId);
		$recordsToInsert = [];
		$questionsIds = [];
		$meta = $request->get('meta');

		if (!Auth::user()->can('view', $user)) {
			return $this->respondForbidden();
		}

		foreach ($results as $result) {
			$questionId = $result['questionId'];
			$answerId = $result['answerId'];
			$question = QuizQuestion::find($questionId);
			$answer = QuizAnswer::find($answerId);

			if (!empty($question) && !empty($answer)) {
				$recordsToInsert[] = [
					'quiz_question_id' => $questionId,
					'quiz_answer_id' => $answerId,
					'user_id' => $userId,
					'created_at' => Carbon::now(),
				];

				$questionsIds[] = $questionId;
			}
		}

		if (count($recordsToInsert) > 100) {
			\Log::notice('>>>More than 100 answers send. Possible exam');
		}

		if (!empty($meta['examMode']) && !empty($meta['examTagId'])) {
			$examTagId = $meta['examTagId'];
			\Log::notice('>>>Dispatching CalculateExamResults Job');
			$course = Course::find(CourseProvider::getCourseId());

			if (($meta['allQuestionsSolved'] ?? false) && $course->entry_exam_tag_id === $meta['examTagId']) {
				$user->has_finished_entry_exam = true;
				$user->save();
			}

			$this->dispatch(new CalculateExamResults($examTagId, $userId, $recordsToInsert));
		}

		UserQuizResults::insert($recordsToInsert);

		UserPlanProgress
			::where('user_id', $userId)
			->whereIn('question_id', $questionsIds)
			->where('resolved_at', null)
			->update(['resolved_at' => Carbon::today()]);

		$this->respondOk();
	}

	public function getQuiz($userId, $quizId)
	{
		$values = Redis::get(self::getQuizRedisKey($userId, $quizId));

		if (!empty($values)) {
			$quiz = json_decode($values);
		} else {
			$quiz = [];
		}
		return $this->json([
			'quiz' => $quiz
		]);
	}

	public function putQuiz(Request $request, $userId, $quizId)
	{
		$quiz = $request->quiz;
		$recordedAnswers = $request->recordedAnswers;

		try {
			if (!empty($recordedAnswers)) {
				$recordedAnswersWithTimestamps = array_map(function($results) {
					$results['created_at'] = Carbon::now();
					return $results;
				}, $recordedAnswers);

				UserQuizResults::insert($recordedAnswersWithTimestamps);

				UserPlanProgress
					::where('user_id', $userId)
					->whereIn('question_id', collect($recordedAnswers)->pluck('quiz_question_id')->toArray())
					->where('resolved_at', null)
					->update(['resolved_at' => Carbon::today()]);

			}
		} catch
		(QueryException $e) {
			throw $e;
		} finally {
			Redis::set(self::getQuizRedisKey($userId, $quizId), json_encode($quiz));
		}

		return $this->respondOk();
	}

	public function delete($userId) {
		if (Auth::user()->id !== (int) $userId) {
			return $this->respondForbidden();
		}

		UserQuizResults::where('user_id', $userId)->delete();
		UserPlanProgress::where('user_id', $userId)->delete();
		$keyPattern = self::getQuizRedisKey($userId, '*');
		$allKeys = Redis::keys($keyPattern);
		foreach ($allKeys as $key) {
			Redis::del($key);
		}

		return $this->respondOk();
	}

	static function getQuizRedisKey($userId, $quizId)
	{
		return sprintf(self::KEY_QUIZ_TEMPLATE, $quizId, $userId, self::CACHE_VERSION);
	}
}
