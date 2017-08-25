<?php namespace App\Http\Controllers\Api\Filters;


use App\Http\Controllers\Api\Concerns\TranslatesApiQueries;

class TagsFilter extends ApiFilter
{
	use TranslatesApiQueries;

	protected $expected = [];

	public function apply($model)
	{
		return $model->whereHas('tags', function($query) {
			$query->whereIn('tags.id', $this->params);
		});
	}
}
