<?php namespace App\Http\Controllers\Api\Filters;


class QueryFilter extends ApiFilter
{
	public function apply($model)
	{
		return $model;
	}
}
