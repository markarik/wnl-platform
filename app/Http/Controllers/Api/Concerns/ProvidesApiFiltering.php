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

		$model = $this->addFilters($request->filters, $model);

		if (!empty($randomize)) {
			$response = $this->randomizedResponse($model, $this->limit);
		} else {
			$response = $this->paginatedResponse($model, $this->limit, $this->page);
		}

		return $this->respondOk($response);
	}

	public function filterList(Request $request)
	{
		$resource = $request->route('resource');
		$model = app(static::getResourceModel($resource));

		$items = $this->getCounters($request, $model);

		return $this->respondOk($items);
	}

	protected function getCounters($request, $model)
	{
		$available = [];
		foreach (static::AVAILABLE_FILTERS as $filterName) {
			$filter = $this->getFilter($filterName);
			$filters = $this->filtersExcept($request->filters, $filterName);
			$builder = $this->addFilters($filters, $model);
			$counters = $filter->count($builder);
			if ($counters) {
				$available[$filterName] = $counters;
			}
		}

		return $available;
	}

	protected function addFilters($filters, $model)
	{
		foreach ($filters as $filter) {
			$filterName = array_keys($filter)[0];
			$params = $filter[$filterName];
			$this->checkIsArray($filter);
			$this->checkIsArray($params);
			$oFilter = $this->getFilter($filterName);
			$model = $oFilter->apply($model, $params);
		}

		return $model;
	}

	protected function getFilter($filterName)
	{
		$filterClass = $this->getFilterClass($filterName);

		return new $filterClass();
	}

	protected function getFilterClass($filterName)
	{
		$className = collect(explode('-', $filterName))
				->map(function ($v) {
					return studly_case($v);
				})
				->implode('\\') . 'Filter';

		return 'App\Http\Controllers\Api\Filters\\' . $className;
	}

	protected function checkIsArray($filter)
	{
		if (is_array($filter)) return;

		throw new ApiFilterException('Filter must be an array of arrays.');
	}

	protected function randomizedResponse($model, $limit)
	{
		$data = $model
			->inRandomOrder()
			->limit($limit)
			->get();

		return [
			'data' => $this->transform($data),
		];
	}

	protected function searchFilters($request, $filterName)
	{
		if (empty($request->filters)) return [];

		$filtered = collect($request->filters)
			->filter(function ($val, $key) use ($filterName) {
				return array_key_exists($filterName, $val);
			});

		if ($filtered->count() > 0) {
			$first = $filtered->shift();

			return $first[$filterName];
		}

		return [];
	}

	protected function filtersExcept($filters, $except)
	{
		if (empty($filters)) return [];

		$filtered = collect($filters)
			->filter(function ($val, $key) use ($except) {
				return !array_key_exists($except, $val);
			});

		return $filtered->toArray();
	}
}
