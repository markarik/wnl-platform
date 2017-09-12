<?php namespace App\Http\Controllers\Api\PrivateApi;

use Auth;
use App\Models\User;
use App\Models\UserQuizResults;
use App\Models\UserPlanProgress;
use App\Models\QuizQuestion;
use App\Models\QuizAnswer;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use League\Fractal\Resource\Collection;
use App\Http\Controllers\Api\ApiController;
use App\Http\Controllers\Api\Transformers\UserQuizResultsTransformer;
use Illuminate\Database\QueryException;


class UserQuizResultsApiController extends ApiController
{
	// quizSetId - userId - cacheVersion
	const KEY_QUIZ_TEMPLATE = 'UserState:Quiz:%s:%s:%s';
	const CACHE_VERSION = 1;

	public function __construct(Request $request)
	{
		parent::__construct($request);
		$this->resourceName = config('papi.resources.quiz_results');
	}

	public function get($userId)
	{
		$user = User::fetch($userId);

		if (!Auth::user()->can('view', $user)) {
			return $this->respondUnauthorized();
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

		if (!Auth::user()->can('view', $user)) {
			return $this->respondUnauthorized();
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
					'user_id' => $userId
				];

				$questionsIds[] = $questionId;
			}
		}

		UserQuizResults::insert($recordsToInsert);

		UserPlanProgress
			::where('user_id', $userId)
			->whereIn('question_id', $questionsIds)
			->where('resolved_at', null)
			->update(['resolved_at' => Carbon::today()]);

		$this->respondOk();
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
				UserQuizResults::insert($recordedAnswers);

				UserPlanProgress
					::where('user_id', $id)
					->whereIn('question_id', array_map(function($results) {
						return $results['quiz_question_id'];
					}, $recordedAnswers))
					->where('resolved_at', null)
					->update(['resolved_at' => Carbon::today()]);

			}
		} catch
		(QueryException $e) {
			throw $e;
		} finally {
			Redis::set(self::getQuizRedisKey($id, $quizId), json_encode($quiz));
		}

		return $this->respondOk();
	}

	static function getQuizRedisKey($userId, $quizId)
	{
		return sprintf(self::KEY_QUIZ_TEMPLATE, $quizId, $userId, self::CACHE_VERSION);
	}
}