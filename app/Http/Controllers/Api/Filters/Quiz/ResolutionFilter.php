<?php namespace App\Http\Controllers\Api\Filters\Quiz;


use App\Http\Controllers\Api\Filters\ApiFilter;

class ResolutionFilter extends ApiFilter
{
	protected $expected = ['user_id', 'list'];

	public function apply($model)
	{
		$model = $model->where(function ($query) {
			foreach ($this->params['list'] as $state) {
				$query->orWhere(function ($query) use ($state) {
					if (method_exists($this, $state)) {
						$this->{$state}($query);
					}
				});
			}
		});

		return $model;
	}

	private function incorrect($query)
	{
		$query->whereHas('userQuizResults', function ($query) {
			$query
				->where('user_id', $this->params['user_id'])
				->whereHas('quizAnswer', function ($query) {
					$query->where('is_correct', false);
				});
		});
	}

	protected function correct($query)
	{
		$query->whereHas('userQuizResults', function ($query) {
			$query
				->where('user_id', $this->params['user_id'])
				->whereHas('quizAnswer', function ($query) {
					$query->where('is_correct', true);
				});
		});
	}

	protected function unresolved($query)
	{
		$query->whereDoesntHave('userQuizResults', function ($query) {
			$query->where('user_id', $this->params['user_id']);
		});
	}
}
