<?php namespace App\Http\Controllers\Api\Filters\ByTaxonomy;


class SubjectsFilter extends ByTaxonomyFilter
{
	public function count($builder)
	{
		return parent::taxonomyCounters($builder, 'subjects');
	}
}
