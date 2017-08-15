<?php namespace App\Http\Controllers\Api\Filters;


class CategoriesFilter extends ApiFilter
{
	public function apply($model)
	{
		return $model;
	}
}
