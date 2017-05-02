<?php

namespace App\Http\Controllers\Api;

use App\Models\Lesson;
use App\Models\QnaQuestion;
use Illuminate\Http\Request;
use Auth;

class QuestionsApiController extends ApiController
{
	public function __construct(Request $request)
	{
		parent::__construct($request);
		$this->resourceName = config('papi.resources.questions');
	}

	public function post(Request $request)
	{
		if (!$request->has('lessonId') || !$request->has('text')) {
			return $this->respondInvalidInput('lessonId and text must be present in the request.');
		}

		$lessonId = $request->get('lessonId');
		$text = $request->get('text');

		if (empty($text)) {
			return $this->respondInvalidInput('text can\'t be empty.');
		}

		$lesson = Lesson::find($lessonId);

		if (!$lesson) {
			return $this->respondInvalidInput('Lesson with given id does\'n exist.');
		}

		$question = QnaQuestion::create([
			'text'    => $text,
			'user_id' => Auth::user()->id,
		]);

		$question->tags()->attach($lesson->tags);

		$data = ['id' => $question->id];

		return response()->json($data, 201);
	}
}
