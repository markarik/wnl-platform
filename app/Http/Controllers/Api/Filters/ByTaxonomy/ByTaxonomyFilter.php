<?php namespace App\Http\Controllers\Api\Filters\ByTaxonomy;


use App\Http\Controllers\Api\Filters\ApiFilter;

abstract class ByTaxonomyFilter extends ApiFilter
{
	protected function getChildItems($expectedParent, $list)
	{
		return $list->first(function ($value, $key) use ($expectedParent) {
			return $key === $expectedParent;
		});
	}

	protected function buildChildStructure($tagId, $groupedTags, $structure)
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
			];

			$childStructure = $this->buildChildStructure($rootItem->tag->id, $groupedTags, []);

			if (!empty($childStructure)) {
				$entry['items'] = $childStructure;
			}

			$structure[] = $entry;
		}

		return $structure;
	}

	protected function buildTaxonomyStructure($taxonomyName)
	{
		$groupedTags = Taxonomy::select()
			->where('name', $taxonomyName)
			->first()
			->tagsTaxonomy
			->sortBy('order_number')
			->groupBy('parent_tag_id');

		$items = $this->buildChildStructure(0, $groupedTags, []);

		return $items;
	}

	public function count($builder)
	{
		$model = $builder->getModel();

		$result = $model::searchRaw(
			$this->elasticCraziness()
		);

		return ['whatever' => $result];
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
							'field' => 'tags',
							'size'  => 300, // <- TODO: Resolve dynamically
						],
					],
				],
			],
		];
	}
}
