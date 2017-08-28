<?php namespace App\Http\Controllers\Api\Concerns;

use App\Exceptions\ApiFilterException;
use Illuminate\Http\Request;

trait ProvidesApiFiltering
{
	public $defaultLimit = 30;

	public $limit;

	public function filter(Request $request)
	{
		$resource = $request->route('resource');
		$model = app(static::getResourceModel($resource));
		$this->limit = $request->limit ?? $this->defaultLimit;
		$this->page = $request->page ?? 1;
		$randomize = $request->randomize;

		$model = $this->addFilters($request, $model);

		if (!empty($randomize)) {
			$response = $this->randomizedResponse($model, $this->limit);
		} else {
			$response = $this->paginatedResponse($model, $this->limit, $this->page);
		}

		$response = array_merge($response, $this->listFilters($model));

		return $this->respondOk($response);
	}

	public function listFilters($builder)
	{
		$available = [];
		foreach (static::AVAILABLE_FILTERS as $filterName) {
			$filter = $this->getFilter($filterName);
			$available[$filterName] = $filter->count($builder);
		}

		return compact('available');
	}

	protected function addFilters($request, $model)
	{
		if (empty($request->filters)) {
			return $model;
		}

		foreach ($request->filters as $filter) {
			$filterName = array_keys($filter)[0];
			$params = $filter[$filterName];
			$this->checkIsArray($filter);
			$this->checkIsArray($params);
			$oFilter = $this->getFilter($filterName);
			$model = $oFilter->apply($model, $params);
		}

		return $model;
	}

	private function getFilter($filterName)
	{
		$filterClass = $this->getFilterClass($filterName);

		return new $filterClass();
	}

	private function getFilterClass($filterName)
	{
		$className = collect(explode('.', $filterName))
				->map(function ($v) {
					return studly_case($v);
				})
				->implode('\\') . 'Filter';

		return 'App\Http\Controllers\Api\Filters\\' . $className;
	}

	private function checkIsArray($filter)
	{
		if (is_array($filter)) return;

		throw new ApiFilterException('Filter must be an array of arrays.');
	}

	private function randomizedResponse($model, $limit)
	{
		$data = $model
			->inRandomOrder()
			->limit($limit)
			->get();

		return [
			'data' => $this->transform($data),
		];
	}
}
