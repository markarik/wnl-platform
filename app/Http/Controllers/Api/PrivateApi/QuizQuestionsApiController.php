<?php namespace App\Http\Controllers\Api\PrivateApi;

use Illuminate\Http\Request;
use App\Http\Controllers\Api\ApiController;

class QuizQuestionsApiController extends ApiController
{
	public function __construct(Request $request)
	{
		parent::__construct($request);
		$this->resourceName = config('papi.resources.quiz-questions');
	}

	public function post($request)
	{
		// $question = $request->get('question');
		// $answers = $request->get('answers');
		// $user = Auth::user();

		// $question = QnaQuestion::create([
		// 	'text'    => $text,
		// 	'user_id' => $user->id,
		// 	'meta' => ['context' => $context]
		// ]);

		// foreach ($tags as $tag) {
		// 	$question->tags()->attach(
		// 		Tag::firstOrCreate(['id' => $tag])
		// 	);
		// }

		// $resource = new Item($question, new QnaQuestionTransformer, $this->resourceName);
		// $data = $this->fractal->createData($resource)->toArray();

		// return $this->respondOk($data);
	}

	public function put(UpdateQuestion $request)
	{
		// $qnaQuestion = QnaQuestion::find($request->route('id'));

		// if (!$qnaQuestion) {
		// 	return $this->respondNotFound();
		// }

		// $qnaQuestion->update([
		// 	'text' => $request->input('text'),
		// ]);

		// return $this->respondOk();
	}
}
