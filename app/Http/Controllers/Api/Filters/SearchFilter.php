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
		$results = $model::searchRaw([
				'body' => [
					'_source' => ['id'],
					'query'   => [
						'query_string' => [
							'query'      => $this->params['phrase'],
							'all_fields' => true,
						],
					],
				],
			]) ['hits']['hits'] ?? [];

		$siema = [];
		foreach ($results as $result) {
			array_push($siema, $result['_source']['id']);
		}

		return $builder->whereIn('id', $siema);
	}

	private function checkIsSearchable($model)
	{

		if (array_key_exists(Searchable::class, class_uses($model))) return;

		throw new ApiFilterException('Requested data model is not searchable.');
	}

	public function count($builder)
	{
		return [
			'items'   => [],
			'message' => 'search',
			'type'    => 'search',
		];
	}
}
