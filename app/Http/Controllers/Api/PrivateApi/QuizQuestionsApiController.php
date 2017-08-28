<?php namespace App\Http\Controllers\Api\PrivateApi;

use Illuminate\Http\Request;
use App\Http\Controllers\Api\ApiController;
use App\Models\QuizQuestion;
use App\Http\Requests\Quiz\UpdateQuizQuestion;

class QuizQuestionsApiController extends ApiController
{
	public function __construct(Request $request)
	{
		parent::__construct($request);
		$this->resourceName = config('papi.resources.quiz-questions');
	}

	public function post($request)
	{
		return $this->respondOk($data);
	}

	public function put(UpdateQuizQuestion $request)
	{
		$question = QuizQuestion::find($request->route('id'));

		if (!$question) {
			return $this->respondNotFound();
		}

		$question->update([
			'text' => $request->input('question'),
		]);

		return $this->respondOk();
	}
}
