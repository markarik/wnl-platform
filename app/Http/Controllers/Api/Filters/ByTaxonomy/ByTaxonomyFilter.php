<?php namespace App\Http\Controllers\Api\Filters\ByTaxonomy;


use App\Http\Controllers\Api\Filters\ApiFilter;
use App\Models\Taxonomy;
use Illuminate\Database\Eloquent\Model;

abstract class ByTaxonomyFilter extends ApiFilter
{
	public function handle($builder)
	{
		return $builder->whereHas('tags', function ($query) {
			$query->whereIn('tags.id', $this->params);
		});
	}

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
				'count' => $aggregatedTags->get($rootItem->tag->id)['doc_count'] ?? 0,
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
		$ids = [];
		if ($builder instanceof Model) {
			$model = $builder;
		} else {
			$model = $builder->getModel();
			$ids = $builder
				->get(['id'])
				->pluck('id')
				->toArray();
		}

		$result = $model::searchRaw(
			$this->elasticCraziness($ids, $tags)
		);

		$tagsAggregation = $result['aggregations']['tags']['buckets'];

		return $tagsAggregation;
	}

	protected function elasticCraziness($ids, $tags)
	{
		if ($ids !== []) {
			$query = [
				'terms' => ['id' => $ids],
			];
			$size = count($ids);
		} else {
			$query = [
				'terms' => ['tags.id' => $tags],
			];
			$size = count($tags);
		}

		return [
			'body' => [
				'size'  => 0,
				'query' => $query,
				'aggs'  => [
					'tags' => [
						'terms' => [
							'field' => 'tags.id',
							'size'  => $size,
						],
					],
				],
			],
		];
	}
}
