<?php namespace App\Http\Controllers\Api\Filters;


use App\Http\Controllers\Api\Concerns\TranslatesApiQueries;

class TagsFilter extends ApiFilter
{
	use TranslatesApiQueries;

	public function apply($model)
	{
		return $this->parseHasIn($model, ['tags' => ['tags.name', $this->params]]);
	}

	protected function checkFilterParams()
	{
		return;
	}
}
