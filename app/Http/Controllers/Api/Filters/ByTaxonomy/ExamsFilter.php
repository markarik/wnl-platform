<?php namespace App\Http\Controllers\Api\Filters\ByTaxonomy;


class ExamsFilter extends ByTaxonomyFilter
{
	public function count($builder)
	{
		return parent::taxonomyCounters($builder, 'exams');
	}
}
