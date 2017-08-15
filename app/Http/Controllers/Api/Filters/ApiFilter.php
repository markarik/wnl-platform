<?php namespace App\Http\Controllers\Api\Filters;


abstract class ApiFilter
{
	/**
	 * Apply filter to model
	 *
	 * @param $model
	 *
	 * @return mixed
	 */
	public abstract function apply($model);
}
