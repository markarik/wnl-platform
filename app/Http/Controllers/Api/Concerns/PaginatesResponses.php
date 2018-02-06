<?php namespace App\Http\Controllers\Api\Concerns;

use Auth;
use Cache;

trait PaginatesResponses
{
	public $defaultLimit = 30;
	public $limit;

	/**
	 * @param $model
	 * @param $limit
	 *
	 * @return array
	 */
	protected function paginatedResponse($model, $limit = null, $page = 1)
	{
		$paginator = $model->paginate($limit, ['*'], 'page', $page);
		$limit = $limit || $this->defaultLimit;

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
		$collection = $model->get();
		$userId = Auth::user()->id;

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
		$paginator = $model->paginate($limit, ['*'], 'page', $page);

		if ($paginator->lastPage() < $page) {
			$paginator = $model->paginate($limit, ['*'], 'page', $paginator->lastPage());
		}

		$meta = [
			'total'        => $paginator->total(),
			'last_page'    => $paginator->lastPage(),
			'per_page'     => $paginator->perPage(),
			'cache_hash'   => $cacheKeyPrefix
		];

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
