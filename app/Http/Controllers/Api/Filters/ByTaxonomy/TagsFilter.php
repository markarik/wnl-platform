<?php namespace App\Http\Controllers\Api\Filters\ByTaxonomy;


class TagsFilter extends ByTaxonomyFilter
{
	public function count($builder)
	{
		$taxonomyTags = $this->getTaxonomyTags('tags');
		$tagIds = $taxonomyTags->pluck('tag_id');

		$aggregatedTags = collect(
			$this->fetchAggregation($builder, $tagIds)
		)->keyBy('key'); // :D

		$structure = $this->buildTaxonomyStructure($taxonomyTags, $aggregatedTags);

		return [
			'type'  => 'tags',
			'items' => $structure,
		];
	}
}
