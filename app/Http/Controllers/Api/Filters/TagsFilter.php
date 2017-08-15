<?php namespace App\Http\Controllers\Api\Filters;


class TagsFilter extends ApiFilter
{
	public function apply($model)
	{
		return $model;
	}
}
