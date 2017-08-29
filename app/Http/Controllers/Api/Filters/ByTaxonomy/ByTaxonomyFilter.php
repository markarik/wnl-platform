<?php namespace App\Http\Controllers\Api\Filters\ByTaxonomy;


use App\Http\Controllers\Api\Filters\ApiFilter;
use App\Models\Taxonomy;

abstract class ByTaxonomyFilter extends ApiFilter
{
	protected function getChildItems($expectedParent, $list)
	{
		return $list->first(function ($value, $key) use ($expectedParent) {
			return $key === $expectedParent;
		});
	}

	protected function buildChildStructure($tagId, $groupedTags, $structure, $aggregatedTags)
	{
		if (!$groupedTags->has($tagId)) {
			return null;
		}

		$root = $this->getChildItems($tagId, $groupedTags);

		foreach ($root as $rootItem) {
			$entry = [];
			$entry = [
				'name'  => $rootItem->tag->name,
				'value' => $rootItem->tag->id,
				'count' => $aggregatedTags->get($rootItem->tag->id)['doc_count']
			];

			$childStructure = $this->buildChildStructure($rootItem->tag->id, $groupedTags, [], $aggregatedTags);

			if (!empty($childStructure)) {
				$entry['items'] = $childStructure;
			}

			$structure[] = $entry;
		}

		return $structure;
	}

	protected function buildTaxonomyStructure($taxonomyTags, $aggregatedTags)
	{
		$groupedTags = $taxonomyTags
			->sortBy('order_number')
			->groupBy('parent_tag_id');

		$items = $this->buildChildStructure(0, $groupedTags, [], $aggregatedTags);

		return $items;
	}

	protected function getTaxonomyTags($taxonomyName)
	{
		return Taxonomy::select()
			->where('name', $taxonomyName)
			->first()
			->tagsTaxonomy;
	}

	public function fetchAggregation($builder, $tags)
	{
		$model = $builder->getModel();

		$result = $model::searchRaw(
			$this->elasticCraziness()
		);

		$tagsAggregation = $result['aggregations']['tags']['buckets'];

		return $tagsAggregation;
	}

	protected function elasticCraziness()
	{
		return [
			'body' => [
				'size'  => 0,
				'query' => [
					'match_all' => new \stdClass(), // <- TODO: Resolve dynamically
				],
				'aggs'  => [
					'tags' => [
						'terms' => [
							'field' => 'tags.id',
							'size'  => 300, // <- TODO: Resolve dynamically
						],
					],
				],
			],
		];
	}
}
