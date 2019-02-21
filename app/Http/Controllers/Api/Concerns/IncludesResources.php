<?php namespace App\Http\Controllers\Api\Concerns;


trait IncludesResources
{
	/**
	 * Determine whether a resource should be included.
	 *
	 * @param string $name
	 *
	 * @return bool
	 */
	public static function shouldInclude($name)
	{
		return str_is("*{$name}*", \Request::get('include'));
	}
}
