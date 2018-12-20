<?php namespace App\Http\Controllers\Api\Filters;

class TaggableFilter extends ApiFilter
{
	protected $expected = ['tag_id'];

	public function handle($builder)
	{
		return $this->collection($builder);
	}

	protected function collection($builder)
	{
		return $builder->where('tag_id', $this->params['tag_id']);
	}
}
