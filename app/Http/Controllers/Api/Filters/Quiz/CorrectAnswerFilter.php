<?php namespace App\Http\Controllers\Api\Filters\Quiz;


use App\Http\Controllers\Api\Filters\ApiFilter;

class CorrectAnswerFilter extends ApiFilter
{
	public function apply($model)
	{
		return $model;
	}
}
