<?php namespace App\Http\Controllers\Api\Filters;


class TaxonomyTermsFilter extends ApiFilter
{
	protected $expected = [];

	public function handle($model)
	{
		return $model->whereHas('taxonomyTerms', function ($query) {
			$query->whereIn('taxonomy_terms.id', $this->params);
		});
	}
}
