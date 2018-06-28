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

	public function taxonomyCounters($builder, $taxonomyName = '')
	{
		$taxonomyTags = $this->getTaxonomyTags($taxonomyName);
		$tagIds = $taxonomyTags->pluck('tag_id');

		$aggregatedTags = collect(
			$this->fetchAggregation($builder, $tagIds)
		)->keyBy('key'); // :D

		$structure = $this->buildTaxonomyStructure($taxonomyTags, $aggregatedTags);

		return [
			'type'    => 'tags',
			'message' => $taxonomyName,
			'items'   => $structure,
		];
	}

	protected function buildTaxonomyStructure($taxonomyTags, $aggregatedTags)
	{
		$groupedTags = $taxonomyTags
			->sortBy('order_number')
			->groupBy('parent_tag_id');

		$items = $this->buildChildStructure(0, $groupedTags, [], $aggregatedTags);

		return $items;
	}

	protected function buildChildStructure($tagId, $groupedTags, $structure, $aggregatedTags)
	{
		if (!$groupedTags->has($tagId)) {
			return null;
		}

		$root = $this->getChildItems($tagId, $groupedTags);

		foreach ($root as $rootItem) {
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

	protected function getChildItems($expectedParent, $list)
	{
		return $list->first(function ($value, $key) use ($expectedParent) {
			return $key === $expectedParent;
		});
	}

	protected function getTaxonomyTags($taxonomyName)
	{
		return Taxonomy::select()
			->where('name', $taxonomyName)
			->first()
			->tagsTaxonomy()
			->with('tag')
			->get();
	}

	public function fetchAggregation($builder, $tags, $matchAll = true)
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

		return $this->fetchAggregationByIds($model, $ids, $tags, $matchAll);
	}

	public function fetchAggregationByIds($model, $ids, $tags, $matchAll = true)
	{
		$result = $model::searchRaw(
			$this->elasticCraziness($ids, $tags, $matchAll)
		);

		$tagsAggregation = $result['aggregations']['tags']['buckets'];

		return $tagsAggregation;
	}

	protected function elasticCraziness($ids, $tags, $matchAll)
	{
		if ($ids === [] && $matchAll) {
			$query = [
				'match_all' => new \stdClass(),
			];
		} else {
			$query = [
				'terms' => ['id' => $ids],
			];
		}

		if ($ids !== []) {
			$size = count($ids);
		} else {
			$size = $tags->count();
		}
		$include = $tags->toArray();

		return [
			'body' => [
				'size'  => 0,
				'query' => $query,
				'aggs'  => [
					'tags' => [
						'terms' => [
							'field'   => 'tags.id',
							'size'    => $size,
							'include' => $include,
						],
					],
				],
			],
		];
	}
}
