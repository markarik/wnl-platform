<?php namespace App\Http\Controllers\Api\Filters;


use Illuminate\Database\Eloquent\Builder;

class TaggableFilter extends ApiFilter
{
	protected $expected = ['tag_id'];

	/**
	 * Apply filter to the builder.
	 *
	 * @param Builder $builder
	 *
	 * @return Builder
	 */
	protected function handle($builder)
	{
		return $this->collection($builder);
	}

	/**
	 * Apply filter to the builder.
	 *
	 * @param Builder $builder
	 *
	 * @return Builder
	 */
	protected function collection($builder)
	{
		if (!empty($this->params['taggable_types'])) {
			$builder = $builder->whereIn('taggable_type', $this->params['taggable_types']);
		}

		return $builder->where('tag_id', $this->params['tag_id']);
	}
}
