<?php namespace App\Http\Controllers\Api\PrivateApi;

use Illuminate\Http\Request;
use App\Http\Controllers\Api\ApiController;
use App\Models\QuizQuestion;
use App\Models\Tag;
use App\Http\Requests\Quiz\UpdateQuizQuestion;
use App\Http\Controllers\Api\Transformers\QuizQuestionTransformer;
use League\Fractal\Resource\Item;

class QuizQuestionsApiController extends ApiController
{
	public function __construct(Request $request)
	{
		parent::__construct($request);
		$this->resourceName = config('papi.resources.quiz-questions');
	}

	public function post(UpdateQuizQuestion $request)
	{
		$question = QuizQuestion::create(['text' => $request->input('question')]);

		$resource = new Item($question, new QuizQuestionTransformer, $this->resourceName);

		$data = $this->fractal->createData($resource)->toArray();

		return $this->respondOk($data);
	}

	public function put(UpdateQuizQuestion $request)
	{
		$question = QuizQuestion::find($request->route('id'));

		if (!$question) {
			return $this->respondNotFound();
		}

		if ($request->has('tags')) {
			$question->tags()->detach();

			foreach($request->tags as $tag) {
				$tagModel = Tag::firstOrCreate(['id' => $tag['id'] ]);

				$question->tags()->attach($tagModel);
			}
		}

		$question->update([
			'text' => $request->input('question'),
		]);

		return $this->respondOk();
	}
}
