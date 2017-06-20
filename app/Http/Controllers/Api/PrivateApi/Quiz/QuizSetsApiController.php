<?php

namespace App\Http\Controllers\Api\PrivateApi\Quiz;

use App\Http\Controllers\Api\ApiController;
use App\Http\Controllers\Api\Transformers\QuizSetTransformer;
use App\Http\Requests\Quiz\UpdateQuizSet;
use App\Models\QuizSet;
use App\Models\UserQuizResults;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use League\Fractal\Resource\Item;

class QuizSetsApiController extends ApiController
{
	public function __construct(Request $request)
	{
		parent::__construct($request);
		$this->resourceName = config('papi.resources.quiz-sets');
	}

	public function post(UpdateQuizSet $request)
	{
		$screen = QuizSet::create($request->all());

		$resource = new Item($screen, new QuizSetTransformer, $this->resourceName);
		$data = $this->fractal->createData($resource)->toArray();

		return $this->respondOk($data);
	}

	public function getStats(Request $request, $id)
	{
		$recordedAnswers = UserQuizResults::whereIn('quiz_question_id', function($query) use ($id) {
			$query->select('quiz_question_id')->from('quiz_question_quiz_set')->where('quiz_set_id', $id);
		})
			->select('quiz_question_id', 'quiz_answer_id')
			->get(['quiz_question_id'])
			->toArray();

		$stats = [];
		foreach($recordedAnswers as $recordedAnswer) {
			$count = empty($stats[$recordedAnswer['quiz_question_id']][$recordedAnswer['quiz_answer_id']])
				? 0 : $stats[$recordedAnswer['quiz_question_id']][$recordedAnswer['quiz_answer_id']];
			$stats[$recordedAnswer['quiz_question_id']][$recordedAnswer['quiz_answer_id']] = $count + 1;
		}


		return $this->json([
			'stats' => $stats
		]);
	}
}
