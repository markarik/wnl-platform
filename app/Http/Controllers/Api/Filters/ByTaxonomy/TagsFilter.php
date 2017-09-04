<?php namespace App\Http\Controllers\Api\Filters\ByTaxonomy;


class TagsFilter extends ByTaxonomyFilter
{
	public function count($builder)
	{
		return parent::taxonomyCounters($builder, 'tags');
	}
}
