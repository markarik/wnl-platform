<?php namespace App\Http\Controllers\Api\Filters;


use App\Http\Controllers\Api\Concerns\TranslatesApiQueries;

class QueryFilter extends ApiFilter
{
	use TranslatesApiQueries;

	protected $expected = [];

	public function handle($builder)
	{
		return $this->parseQuery($builder, $this->params);
	}
}
