<?php namespace App\Http\Controllers\Api\Filters\Quiz;


use App\Http\Controllers\Api\Filters\ApiFilter;
use App\Models\UserQuizResults;
use Auth;

class ResolutionFilter extends ApiFilter
{
	protected $expected = ['user_id', 'list'];

	public function handle($builder)
	{
		$builder = $builder->where(function ($query) {
			foreach ($this->params['list'] as $state) {
				$query->orWhere(function ($query) use ($state) {
					$this->{$state}($query);
				});
			}
		});

		return $builder;
	}

	public function values()
	{
		return ['unresolved', 'lastly_correct', 'lastly_incorrect', 'incorrect'];
	}

	public function count($builder)
	{
		$items = [];
		$this->params['user_id'] = Auth::user()->id;

		foreach ($this->values() as $value) {
			$count = (clone $builder)
				->where(function ($query) use ($value) {
					$this->$value($query);
				})->count();

			$items[$value] = [
				'count' => $count,
				'value' => $value,
			];
		}

		return [
			'items'   => $items,
			'type'    => 'list',
			'message' => 'resolution',
			'is_user_specific' => true
		];
	}

	protected function incorrect($query)
	{
		$userId = $this->params['user_id'];

//		return $query->whereHas('userQuizResults', function ($query) {
//			$query
//				->where('user_id', $this->params['user_id'])
//				->whereHas('quizAnswer', function ($query) {
//					$query->where('is_correct', false);
//				});
//		});
		return $query->whereRaw(
			"id in (select user_quiz_results.quiz_question_id from user_quiz_results inner join quiz_answers on user_quiz_results.quiz_answer_id = quiz_answers.id where user_id = {$userId} and is_correct = 0 group by user_quiz_results.quiz_question_id)"
		);
	}

	protected function lastly_correct($query)
	{
		$mostRecentResults = $this->getMostRecentResultForQuestions();

		$onlyCorrect = $mostRecentResults->filter(function($question) {
			return $question;
		});

		return $query->whereIn('id', $onlyCorrect->keys());
	}

	protected function lastly_incorrect($query)
	{
		$mostRecentResults = $this->getMostRecentResultForQuestions();

		$onlyIncorrect = $mostRecentResults->filter(function($question) {
			return !$question;
		});

		return $query->whereIn('id', $onlyIncorrect->keys());
	}

	protected function unresolved($query)
	{
		$userId = $this->params['user_id'];

//		return $query->whereDoesntHave('userQuizResults', function ($query) {
//			$query->where('user_id', $this->params['user_id']);
//		});

		return $query->whereRaw("id not in (select quiz_question_id from user_quiz_results where user_id = {$userId} group by quiz_question_id)");
	}

	protected function resolved($query)
	{
		$userId = $this->params['user_id'];

		return $query->whereRaw("id in (select quiz_question_id from user_quiz_results where user_id = {$userId} group by quiz_question_id)");
	}

	private function getMostRecentResultForQuestions() {
		$userId = $this->params['user_id'];

		$questionsList = collect();
		\DB::table('user_quiz_results')
			->selectRaw('user_quiz_results.quiz_question_id as question_id, quiz_answers.is_correct, user_quiz_results.created_at as created_at')
			->join("quiz_answers", "user_quiz_results.quiz_answer_id", "=", "quiz_answers.id")
			->whereRaw("user_quiz_results.user_id = {$userId}")
			->orderBy('user_quiz_results.created_at')
			->get()
			->each(function($question) use ($questionsList) {
				$questionsList->put($question->question_id, $question->is_correct);
			});

		return $questionsList;
	}
}
