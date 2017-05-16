<?php

namespace App\Http\Controllers\Api\PrivateApi\Qna;

use App\Models\QnaQuestion;
use Illuminate\Http\Request;
use Auth;

class AnswersApiController extends ApiController
{
	public function __construct(Request $request)
	{
		parent::__construct($request);
		$this->resourceName = config('papi.resources.answers');
	}

	public function post(Request $request)
	{
		if (!$request->has('questionId') || !$request->has('text')) {
			return $this->respondInvalidInput('questionId and text must be present in the request.');
		}

		$questionId = $request->get('questionId');
		$text = $request->get('text');

		if (empty($text)) {
			return $this->respondInvalidInput('text can\'t be empty.');
		}

		$question = QnaQuestion::find($questionId);

		if (!$question) {
			return $this->respondInvalidInput('Question with given id does\'n exist.');
		}

		$answer = $question->answers()->create([
			'text'    => $text,
			'user_id' => Auth::user()->id,
		]);

		return response()->json(['id' => $answer->id], 201);
	}
}
