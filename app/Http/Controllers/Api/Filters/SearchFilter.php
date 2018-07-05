<?php namespace App\Http\Controllers\Api\Filters;


use App\Exceptions\ApiFilterException;
use ScoutEngines\Elasticsearch\Searchable;

class SearchFilter extends ApiFilter
{
	protected $expected = ['phrase'/*, 'mode'*/];

	public function handle($builder)
	{
//		$this->checkIsSearchable($builder);
		$model = $builder->getModel();
		$count = (clone $builder)->count();
		$results = $model::searchRaw([
				'body' => [
					'_source' => ['id'],
					'size' => $count,
					'query'   => [
						'query_string' => [
							'query'      => $this->params['phrase'],
							'all_fields' => true,
						],
					],
				],
			]) ['hits']['hits'] ?? [];
		$ids = [];
		foreach ($results as $result) {
			array_push($ids, $result['_source']['id']);
		}

		return $builder->whereIn('id', $ids);
	}

	private function checkIsSearchable($model)
	{

		if (array_key_exists(Searchable::class, class_uses($model))) return;

		throw new ApiFilterException('Requested data model is not searchable.');
	}

	private function getSearchFilter($filters) {
		if (empty($filters)) return false;

		$filtered = collect($filters)
			->filter(function ($val, $key) {
				return array_key_exists('search', $val);
			});

		$filtered = $filtered->toArray();

		return array_shift($filtered);
	}

	public function count($builder)
	{
		$items =  [];

		$filter = $this->getSearchFilter($this->request->filters);

		if (!empty($filter)) {
			$items = [['value' => $filter['search']['phrase']]];
		}

		return [
			'items'   => $items,
			'type'    => 'search',
		];
	}
}
