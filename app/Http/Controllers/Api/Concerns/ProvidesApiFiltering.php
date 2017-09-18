<?php namespace App\Http\Controllers\Api\Concerns;

use App\Exceptions\ApiFilterException;
use Illuminate\Http\Request;
use Redis;
use Auth;
use Cache;

trait ProvidesApiFiltering
{
	static $ACTIVE_FILTERS_KEY = 'active-filters-user-%d-resource-%s';

	public $defaultLimit = 30;

	public $limit;

	public function filter(Request $request)
	{
		$resource = $request->route('resource');
		$model = app(static::getResourceModel($resource));
		$this->limit = $request->limit ?? $this->defaultLimit;
		$this->page = $request->page ?? 1;
		$randomize = $request->randomize;

		if (!$request->doNotSaveFilters) {
			$this->saveActiveFilters($request);
		}

		list ($filters, $paths) = $this->getFilters($request);
		$model = $this->addFilters($filters, $model);

		if (!empty($randomize)) {
			$response = $this->randomizedResponse($model, $this->limit);
		} else {
			$response = $this->cachedPaginatedResponse($request, $model, $this->limit, $this->page, $paths);
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
		$default = [$request->filters, []];

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

		return sprintf(self::$ACTIVE_FILTERS_KEY, $userId, $resource);
	}

	protected function cachedPaginatedResponse(Request $request, $model, $limit, $page = 1, $cachedActiveFilters) {
		if (!$request->has('active') || !$request->has('filters')) {
			return $this->paginatedResponse($model, $limit, $page);
		}

		$activeFilters = empty($request->active) ? $cachedActiveFilters : $request->active;

		$collection = $model->get();
		$resource = $request->route('resource');
		$userId = Auth::user()->id;
		$hashedFilters = $this->hashedFilters($activeFilters);
		$cacheTags = $this->getFiltersCacheTags($resource, $userId);

		if (Cache::tags($cacheTags)->has($this->filtersKey($hashedFilters, 1))) {
			$results = Cache::tags($cacheTags)->get($this->filtersKey($hashedFilters, $page));

			if (!empty($results)) {
				$results['data'] = $this->transform($results['raw_data']);
				return $results;
			}

		}

		Cache::tags($cacheTags)->flush();
		$paginator = $model->paginate($limit, ['*'], 'page', $page);

		if ($paginator->lastPage() < $page) {
			$paginator = $model->paginate($limit, ['*'], 'page', $paginator->lastPage());
		}

		$meta = [
			'total'        => $paginator->total(),
			'last_page'    => $paginator->lastPage(),
			'per_page'     => $paginator->perPage(),
		];

		$chunks = $collection->chunk($limit);
		$page = 1;

		foreach($chunks as $chunk) {
			$results = array_merge($meta, [
				'raw_data' => $chunk,
				'has_more' => $page <= $paginator->lastPage(),
				'current_page' => $page,
				'cached' => true,
				'active_filters' => $activeFilters
			]);
			$cacheKey = $this->filtersKey($hashedFilters, $page);

			Cache::tags($cacheTags)->put($cacheKey, $results, 60);
			$page++;
		}

		return array_merge($meta, [
			'data' => $this->transform($paginator->getCollection()),
			'has_more' => $paginator->hasMorePages(),
			'current_page' => $paginator->currentPage()
		]);
	}

	protected function filtersKey($hashedFilters, $page) {
		return "{$hashedFilters}-{$page}";
	}

	protected function hashedFilters($activeFilters) {
		return hash('md5', json_encode($activeFilters));
	}

	protected function getFiltersCacheTags($resource, $userId) {
		return [$resource, 'filters', "user-{$userId}"];
	}
}
