<?php namespace App\Http\Controllers\Api\Concerns;

use App\Exceptions\ApiFilterException;
use Illuminate\Http\Request;
use Redis;
use Auth;

trait ProvidesApiFiltering
{
	static $ACTIVE_FILTERS_KEY = 'active-filters-user-%d-resource-%s';

	public $defaultLimit = 30;

	public $limit;

	public function activeFilters(Request $request) {
		list ($filters, $paths) = $this->getFilters($request);

		return $this->respondOk($paths);
	}

	public function filter(Request $request)
	{
		$resource = $request->route('resource');
		$order = $request->get('order');
		$model = app(static::getResourceModel($resource));
		$userFiltersPersistanceToken = $request->get('token');

		if (!empty ($order)) {
			$model = $this->parseOrder($model, $order);
		}

		$this->limit = $request->limit ?? $this->defaultLimit;
		$this->page = $request->page ?? 1;
		$randomize = $request->randomize;

		if ($request->saveFilters) {
			$this->saveActiveFilters($request);
		}

		list ($filters, $paths) = $this->getFilters($request);
		$model = $this->addFilters($filters, $model);

		if (!empty($randomize)) {
			$response = $this->randomizedResponse($model, $this->limit);
		} else {
			if (!$request->has('active') || empty($filters)) {
				$response = $this->paginatedResponse($model, $this->limit, $this->page);
			} else {
				$cacheTags = $this->getFiltersCacheTags($resource, $userFiltersPersistanceToken);
				$hashedFilters = $this->hashedFilters($filters);

				$response = $this->cachedPaginatedResponse($cacheTags, $hashedFilters, $model, $this->limit, $this->page);
			}
		}

		$response = array_merge($response, ['active' => $paths]);

		return $this->respondOk($response);
	}

	public function filterList(Request $request)
	{
		$resource = $request->route('resource');
		$model = app(static::getResourceModel($resource));
		$filters = $this->getFilters($request);
		$items = $this->getCounters($filters[0], $model);

		return $this->respondOk($items);
	}

	protected function getCounters($filters, $model)
	{
		$available = [];
		foreach (static::AVAILABLE_FILTERS as $filterName) {
			$filter = $this->getFilter($filterName);
			$filteredFilters = $this->filtersExcept($filters, $filterName);
			$builder = $this->addFilters($filteredFilters, $model);
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
			$this->checkIsArray($filter);
			$filterName = array_keys($filter)[0];
			$params = $filter[$filterName];
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

	protected function saveActiveFilters($request)
	{
		if (!$request->has('filters') || !$request->has('active')) return;

		$key = $this->filtersFormatKey($request);
		$data = json_encode([$request->filters, $request->active]);

		Redis::set($key, $data);
	}

	protected function getFilters($request)
	{
		$default = [$request->filters, $request->active];

		if (!$request->useSavedFilters) return $default;

		$key = $this->filtersFormatKey($request);
		$data = Redis::get($key);

		if (!$data) return $default;

		return json_decode($data, true);
	}

	protected function filtersFormatKey($request)
	{
		$userId = Auth::user()->id;
		$resource = $request->route('resource');

		return self::savedFiltersCacheKey($resource, $userId);
	}

	protected function hashedFilters($activeFilters) {
		return hash('md5', json_encode($activeFilters));
	}

	protected function getFiltersCacheTags($resource, $userFiltersPersistanceToken) {
		$userId = Auth::user()->id;

		return ["user-{$userId}-paginated-{$resource}", $userFiltersPersistanceToken];
	}

	public static function savedFiltersCacheKey($resource, $userId) {
		return sprintf(self::$ACTIVE_FILTERS_KEY, $userId, $resource);
	}
}
