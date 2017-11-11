<?php namespace App\Http\Controllers\Api\Filters\Notification;


class ObjectFilter extends ByTaxonomyFilter
{
	public function count($builder)
	{
		return parent::taxonomyCounters($builder, 'subjects');
	}
}
