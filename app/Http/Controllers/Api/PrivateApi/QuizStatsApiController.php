<?php namespace App\Http\Controllers\Api\PrivateApi;

use Illuminate\Http\Request;
use App\Models\UserQuizResults;
use App\Http\Controllers\Api\ApiController;

class QuizStatsApiController extends ApiController
{
	public function __construct(Request $request)
	{
		parent::__construct($request);
		$this->resourceName = config('papi.resources.quiz-stats');
	}

	public function get($id)
	{
		$recordedAnswers = UserQuizResults::whereIn('quiz_question_id', function ($query) use ($id) {
			$query->select('quiz_question_id')->from('quiz_question_quiz_set')->where('quiz_set_id', $id);
		})
			->select('quiz_question_id', 'quiz_answer_id')
			->get(['quiz_question_id'])
			->toArray();
		$stats = [];
		foreach ($recordedAnswers as $recordedAnswer) {
			$count = empty($stats[$recordedAnswer['quiz_question_id']][$recordedAnswer['quiz_answer_id']])
				? 0 : $stats[$recordedAnswer['quiz_question_id']][$recordedAnswer['quiz_answer_id']];
			$stats[$recordedAnswer['quiz_question_id']][$recordedAnswer['quiz_answer_id']] = $count + 1;
		}

		return $this->json([
			'stats' => $stats,
		]);
	}
}
