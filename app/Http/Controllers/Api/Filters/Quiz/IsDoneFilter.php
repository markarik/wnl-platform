<?php namespace App\Http\Controllers\Api\Filters\Quiz;


use App\Http\Controllers\Api\Filters\ApiFilter;

class IsDoneFilter extends ApiFilter
{
	protected $expected = ['user_id', 'done'];

	public function apply($model)
	{
		$statement = $this->params['done'] ? 'whereHas' : 'whereDoesntHave';

		$model = $model->$statement('userQuizResults', function($query) {
			$query->where('user_id', $this->params['user_id']);
		});

		return $model;
	}
}
