<?php namespace App\Http\Controllers\Api\PrivateApi;

use App\Http\Controllers\Api\Transformers\QnaAnswerTransformer;
use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\Qna\PostAnswer;
use App\Http\Requests\Qna\UpdateAnswer;
use App\Models\QnaAnswer;
use Carbon\Carbon;
use League\Fractal\Resource\Item;
use Illuminate\Http\Request;
use App\Models\QnaQuestion;
use Auth;

class QnaAnswersApiController extends ApiController
{
	public function __construct(Request $request)
	{
		parent::__construct($request);
		$this->resourceName = config('papi.resources.qna-answers');
	}

	public function post(PostAnswer $request)
	{
		$questionId = $request->get('question_id');
		$text = $request->get('text');
		$user = Auth::user();

		$question = QnaQuestion::find($questionId);

		if (!$question) {
			return $this->respondNotFound('Question does not exist.');
		}

		$answer = $question->qnaAnswers()->create([
			'text'    => $text,
			'user_id' => $user->id,
		]);

		$resource = new Item($answer, new QnaAnswerTransformer, $this->resourceName);
		$data = $this->fractal->createData($resource)->toArray();

		return $this->respondOk($data);
	}

	public function put(UpdateAnswer $request)
	{
		$qnaAnswer = QnaAnswer::find($request->route('id'));

		if (!$qnaAnswer) {
			return $this->respondNotFound();
		}

		if ($request->has('text')) {
			$qnaAnswer->text = $request->input('text');
		}

		if ($request->has('verified')) {
			$qnaAnswer->verified_at = $request->input('verified') ? Carbon::now() : null;
		}

		$qnaAnswer->save();

		return $this->transformAndRespond($qnaAnswer);
	}

	public function query(Request $request) {
		$qnaAnswerQuery = QnaAnswer::select();

		if ($request->has('user_id')) {
			$qnaAnswerQuery->where('user_id', $request->get('user_id'));
		}

		$qnaAnswers = $qnaAnswerQuery->orderBy('id', 'asc')->get();

		return $this->transformAndRespond($qnaAnswers);
	}
}
