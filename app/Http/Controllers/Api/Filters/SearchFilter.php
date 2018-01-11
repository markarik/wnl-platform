<?php namespace App\Http\Controllers\Api\Filters;


use App\Exceptions\ApiFilterException;
use ScoutEngines\Elasticsearch\Searchable;

class SearchFilter extends ApiFilter
{
	protected $expected = ['phrase', 'mode'];

	public function handle($builder)
	{
		$this->checkIsSearchable($builder);



		return $builder;
	}

	private function checkIsSearchable($model) {
		if (array_key_exists(Searchable::class, class_uses($model))) return;

		throw new ApiFilterException('Requested data model is not searchable.');
	}

	public function count($builder)
	{
		return [
			'items'   => null,
			'message' => 'search',
			'type'    => 'search',
		];
	}
}
