<?php namespace App\Http\Controllers\Api\PrivateApi;

use Auth;
use App\Models\User;
use App\Models\UserQuizResults;
use App\Models\QuizQuestion;
use App\Models\QuizAnswer;
use Illuminate\Http\Request;
use League\Fractal\Resource\Collection;
use App\Http\Controllers\Api\ApiController;
use App\Http\Controllers\Api\Transformers\UserQuizResultsTransformer;

class UserQuizResultsApiController extends ApiController
{
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
			}
		}

		UserQuizResults::insert($recordsToInsert);

		$this->respondOk();
	}
}