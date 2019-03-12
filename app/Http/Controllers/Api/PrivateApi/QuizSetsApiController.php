<?php namespace App\Http\Controllers\Api\PrivateApi;

use App\Models\QuizSet;
use Illuminate\Http\Request;
use League\Fractal\Resource\Item;
use App\Http\Requests\Quiz\UpdateQuizSet;
use App\Http\Controllers\Api\ApiController;
use App\Http\Controllers\Api\Transformers\QuizSetTransformer;

class QuizSetsApiController extends ApiController
{
	public function __construct(Request $request)
	{
		parent::__construct($request);
		$this->resourceName = config('papi.resources.quiz-sets');
	}

	public function post(UpdateQuizSet $request)
	{
		$quizSet = QuizSet::create($request->all());

		if (is_array($request->quiz_questions)) {
			$quizSet->syncQuestions($request->quiz_questions);
		}

		return $this->transformAndRespond($quizSet);
	}
	public function put(UpdateQuizSet $request)
	{
		$quizSet = QuizSet::find($request->route('id'));

		if (!$quizSet) {
			return $this->respondNotFound();
		}

		$quizSet->update($request->all());

		if (is_array($request->quiz_questions)) {
			$quizSet->syncQuestions($request->quiz_questions);
		}

		return $this->transformAndRespond($quizSet);
	}
}
