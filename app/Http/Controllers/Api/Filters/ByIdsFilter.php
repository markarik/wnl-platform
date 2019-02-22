<?php namespace App\Http\Controllers\Api\Filters;


use Illuminate\Database\Eloquent\Builder;

class ByIdsFilter extends ApiFilter
{
	protected $expected = ['ids'];

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
		return $builder->whereIn('id', $this->params['ids']);
	}
}
