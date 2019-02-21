<?php namespace App\Http\Controllers\Api\Concerns;

use Auth;
use Cache;
use Illuminate\Database\Eloquent\Builder;
use League\Fractal\Pagination\Cursor;
use League\Fractal\Resource\Collection;


trait PaginatesResponses
{
	public $defaultLimit = 30;
	public $limit;

	protected function cursorPaginatedResponse($builder, $currentCursor, $limit = 10, $cursorField = 'id', $comparison = '>') {
		if ($currentCursor) {
			$items = $builder->where($cursorField, $comparison, $currentCursor)->take($limit)->get();
		} else {
			$items = $builder->take($limit)->get();
		}

		$newCursor = null;

		if ($items->count() > 0) {
			$newCursor = $items->last()->$cursorField;
		}

		$cursor = new Cursor($currentCursor, null, $newCursor, $items->count());

		$transformed = $this->transform($items);
		$collection = new Collection($transformed);
		$collection->setCursor($cursor);

		return [
			'data' => $this->transform($items),
			'cursor' => [
				'current' => $cursor->getCurrent(),
				'next' => $cursor->getNext(),
				'previous' => $cursor->getPrev(),
				'has_more' => $cursor->getCount() > 0
			]
		];
	}

	/**
	 * @param Builder $model
	 * @param int $limit
	 *
	 * @return array
	 */
	protected function paginatedResponse($model, $limit = null, $page = 1)
	{
		$paginator = $model->paginate($limit, ['*'], 'page', $page);
		$limit = $limit ?? $this->defaultLimit;

		if ($paginator->lastPage() < $page) {
			$paginator = $model->paginate($limit, ['*'], 'page', $paginator->lastPage());
		}

		$response = [
			'data'         => $this->transform($paginator->getCollection()),
			'total'        => $paginator->total(),
			'has_more'     => $paginator->hasMorePages(),
			'last_page'    => $paginator->lastPage(),
			'per_page'     => $paginator->perPage(),
			'current_page' => $paginator->currentPage(),
		];

		return $response;
	}

	protected function cachedPaginatedResponse($cacheTags, $cacheKeyPrefix, $model, $limit, $page = 1) {
		if (Cache::tags($cacheTags)->has($this->cacheKey($cacheKeyPrefix, $page))) {
			$results = Cache::tags($cacheTags)->get($this->cacheKey($cacheKeyPrefix, $page));

			if (!empty($results)) {
				$results['data'] = $this->transform($results['raw_data']);
				$results['cache_hash'] = $cacheKeyPrefix;
				$results['from_cache'] = true;

				return $results;
			}
		}

		Cache::tags($cacheTags)->flush();
		$paginator = (clone $model)->paginate($limit, ['*'], 'page', $page);

		if ($paginator->lastPage() < $page) {
			$paginator = (clone $model)->paginate($limit, ['*'], 'page', $paginator->lastPage());
		}

		$meta = [
			'total'        => $paginator->total(),
			'last_page'    => $paginator->lastPage(),
			'per_page'     => $paginator->perPage(),
			'cache_hash'   => $cacheKeyPrefix
		];

		$collection = $model->get();
		$chunks = $collection->chunk($limit);
		$page = 1;

		foreach($chunks as $chunk) {
			$results = array_merge($meta, [
				'raw_data' => $chunk,
				'has_more' => $page <= $paginator->lastPage(),
				'current_page' => $page,
				'cache_hash' => $cacheKeyPrefix
			]);
			$cacheKey = $this->cacheKey($cacheKeyPrefix, $page);

			Cache::tags($cacheTags)->put($cacheKey, $results, 60 * 24);
			$page++;
		}

		return array_merge($meta, [
			'data' => $this->transform($paginator->getCollection()),
			'has_more' => $paginator->hasMorePages(),
			'current_page' => $paginator->currentPage()
		]);
	}

	private function cacheKey($cacheKeyPrefix, $page) {
		return $cacheKeyPrefix . '-' . $page;
	}
}
