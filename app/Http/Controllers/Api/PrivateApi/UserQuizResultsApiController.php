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
        $questionId = $request->get('questionId');
        $answerId = $request->get('answerId');
        $userId = $request->route('userId');

        $user = User::fetch($userId);
        $question = QuizQuestion::find($questionId);
        $answer = QuizAnswer::find($answerId);

        if (!Auth::user()->can('view', $user)) {
            return $this->respondUnauthorized();
        }

        if (empty($question) || empty($answer)) {
            return $this->respondNotFound();
        }

        UserQuizResults::insert([[
            'quiz_question_id' => $question->id,
            'quiz_answer_id' => $answer->id,
            'user_id' => $user->id
        ]]);

        $this->respondOk();
    }
}