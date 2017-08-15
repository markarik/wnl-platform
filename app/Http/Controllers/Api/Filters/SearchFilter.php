<?php namespace App\Http\Controllers\Api\Filters;


use App\Exceptions\ApiFilterException;
use ScoutEngines\Elasticsearch\Searchable;

class SearchFilter extends ApiFilter
{
	protected $expected = ['phrase', 'mode'];

	public function apply($model)
	{
		$this->checkIsSearchable($model);

		return $model;
	}

	private function checkIsSearchable($model) {
		if (array_key_exists(Searchable::class, class_uses($model))) return;

		throw new ApiFilterException('Requested data model is not searchable.');
	}
}
