<?php namespace App\Http\Controllers\Api\Filters;

class TaggableFilter extends ApiFilter
{
	protected $expected = ['tag_id'];

	public function handle(Builder $builder)
	{
		return $this->collection($builder);
	}

	protected function collection(Builder $builder)
	{
		if (!empty($this->params['taggable_types'])) {
			$builder = $builder->whereIn('taggable_type', $this->params['taggable_types']);
		}

		return $builder->where('tag_id', $this->params['tag_id']);
	}
}
