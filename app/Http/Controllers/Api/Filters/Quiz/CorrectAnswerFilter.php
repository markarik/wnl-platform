<?php namespace App\Http\Controllers\Api\Filters\Quiz;


use App\Http\Controllers\Api\Filters\ApiFilter;

class CorrectAnswerFilter extends ApiFilter
{
	protected $expected = ['user_id', 'correct'];

	public function apply($model)
	{
		$model = $model->whereHas('userQuizResults', function ($query) {
			$query
				->where('user_id', $this->params['user_id'])
				->whereHas('quizAnswer', function ($query) {
					$query->where('is_correct', false);
				});
		});

		return $model;
	}
}
