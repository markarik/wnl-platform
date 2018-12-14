<?php namespace App\Http\Controllers\Api\Filters;


class TagsFilter extends ApiFilter
{
	protected $expected = [];

	public function handle($model)
	{
		return $model->whereHas('tags', function ($query) {
			$query->whereIn('tags.id', $this->params);
		});
	}
}
