<?php

namespace App\Http\Controllers\Api\Concerns;


use Illuminate\Http\Request;
use ScoutEngines\Elasticsearch\Searchable;

trait ProvidesApiFiltering
{

	public $defaultLimit = 30;

	public $limit;

	public function filter(Request $request)
	{
		$resource = $request->route('resource');
		$model = app(static::getResourceModel($resource));
		$this->limit = $request->limit ?? $this->defaultLimit;

		$model = $this->parseFilters($request, $model);
		$response = $this->paginatedResponse($model, $this->limit);

		return $this->respondOk($response);
	}

	private function parseFilters($request, $model)
	{
		if ($request->search) {
			$model = $this->applySearch(
				$model,
				$request->search,
				$this->limit
			);
		}

		return $model;
	}

	protected function applySearch($model, $params, $limit)
	{
		if (!array_key_exists(Searchable::class, class_uses($model))) {
			return $model;
		}

		return $model;
	}
}
