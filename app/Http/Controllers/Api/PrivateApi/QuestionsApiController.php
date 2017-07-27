<?php namespace App\Http\Controllers\Api\PrivateApi;

use App\Http\Controllers\Api\ApiController;
use App\Http\Controllers\Api\Transformers\QnaQuestionTransformer;
use App\Http\Requests\Qna\PostQuestion;
use App\Http\Requests\Qna\UpdateQuestion;
use App\Models\Lesson;
use App\Models\QnaQuestion;
use App\Models\Tag;
use Illuminate\Http\Request;
use Auth;
use League\Fractal\Resource\Item;

class QuestionsApiController extends ApiController
{
	public function __construct(Request $request)
	{
		parent::__construct($request);
		$this->resourceName = config('papi.resources.questions');
	}

	public function post(PostQuestion $request)
	{
		$tags = $request->get('tags');
		$text = $request->get('text');
		$user = Auth::user();

		$question = QnaQuestion::create([
			'text'    => $text,
			'user_id' => $user->id,
		]);

		foreach ($tags as $tag) {
			$question->tags()->attach(
				Tag::firstOrCreate(['id' => $tag])
			);
		}

		$resource = new Item($question, new QnaQuestionTransformer, $this->resourceName);
		$data = $this->fractal->createData($resource)->toArray();

		return $this->respondOk($data);
	}

	public function put(UpdateQuestion $request)
	{
		$qnaQuestion = QnaQuestion::find($request->route('id'));

		if (!$qnaQuestion) {
			return $this->respondNotFound();
		}

		$qnaQuestion->update([
			'text' => $request->input('text'),
		]);

		return $this->respondOk();
	}
}
