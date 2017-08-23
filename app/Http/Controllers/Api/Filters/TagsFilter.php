<?php namespace App\Http\Controllers\Api\Filters;


use App\Http\Controllers\Api\Concerns\TranslatesApiQueries;

class TagsFilter extends ApiFilter
{
	use TranslatesApiQueries;

	protected $expected = [];

	public function apply($model)
	{
		return $this->parseHasIn($model, ['tags' => ['tags.name', $this->params]]);
	}
}
