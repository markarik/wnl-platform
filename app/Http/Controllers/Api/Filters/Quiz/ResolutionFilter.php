<?php namespace App\Http\Controllers\Api\Filters\Quiz;


use App\Http\Controllers\Api\Filters\ApiFilter;

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
		return ['correct', 'incorrect', 'unresolved'];
	}

	public function count($builder)
	{
		$items = [];

		foreach ($this->values() as $value) {
			$items[] = [
				'value' => $value,
				'count' => $this->{$value}($builder)->count(),
			];
		}

		return compact('items');
	}

	protected function incorrect($query)
	{
		return $query->whereHas('userQuizResults', function ($query) {
			$query
				->where('user_id', $this->params['user_id'])
				->whereHas('quizAnswer', function ($query) {
					$query->where('is_correct', false);
				});
		});
	}

	protected function correct($query)
	{
		return $query->whereHas('userQuizResults', function ($query) {
			$query
				->where('user_id', $this->params['user_id'])
				->whereHas('quizAnswer', function ($query) {
					$query->where('is_correct', true);
				});
		});
	}

	protected function unresolved($query)
	{
		return $query->whereDoesntHave('userQuizResults', function ($query) {
			$query->where('user_id', $this->params['user_id']);
		});
	}
}
