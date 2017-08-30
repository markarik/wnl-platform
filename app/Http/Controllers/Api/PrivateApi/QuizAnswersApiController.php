<?php namespace App\Http\Controllers\Api\PrivateApi;

use Illuminate\Http\Request;
use App\Http\Controllers\Api\ApiController;
use App\Models\QuizAnswer;
use App\Http\Requests\Quiz\UpdateQuizAnswer;

class QuizAnswersApiController extends ApiController
{
	public function __construct(Request $request)
	{
		parent::__construct($request);
		$this->resourceName = config('papi.resources.quiz-answers');
	}

	public function post(UpdateQuizAnswer $request)
	{
		return $this->respondOk($data);
	}

	public function put(UpdateQuizAnswer $request)
	{
		$answer = QuizAnswer::find($request->route('id'));

		if (!$answer) {
			return $this->respondNotFound();
		}

		$answer->update([
			'text' => $request->input('text'),
			'is_correct' => $request->input('is_correct')
		]);

		return $this->respondOk();
	}
}
