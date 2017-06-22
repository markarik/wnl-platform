<?php

namespace App\Http\Middleware;

use Cache;
use Closure;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Api\Concerns\GeneratesApiResponses;

class ApiCache
{
	use GeneratesApiResponses;

	private $tags;

	/**
	 * Handle an incoming request.
	 *
	 * @param  \Illuminate\Http\Request $request
	 * @param  \Closure $next
	 * @return mixed
	 */
	public function handle($request, Closure $next)
	{
		$tags = $this->getTags($request);
		$key = $request->getRequestUri();

		if ($this->excluded($request)) {
			return $next($request);
		}

		$cached = Cache::tags($tags)->get($key);

		if ($cached !== null) {
			return $this->respondOk($cached);
		}

		$response = $next($request);

		if ($this->responseValid($response)) {
			Cache::tags($tags)->put($key, $response->getData(), 60 * 24);
		}

		return $response;
	}

	protected function responseValid($response)
	{
		return
			$response instanceof JsonResponse &&
			$response->getStatusCode() === 200;
	}

	protected function excluded($request)
	{
		$excludedTags = ['users', 'profiles', 'reactions', 'orders', 'state', 'quiz_stats'];

		$methodExcluded = !in_array($request->method(), ['GET', 'POST']);
		$queryExcluded = (bool)array_intersect($excludedTags, $this->getTags($request));
		$urlExcluded = str_is('*current*', $request->getRequestUri());
		$postExcluded = $request->method() === 'POST' && !str_is('*.search*', $request->getRequestUri());

		return
			$methodExcluded ||
			$queryExcluded ||
			$urlExcluded ||
			$postExcluded;
	}

	protected function getTags($request)
	{
		if (!empty($this->tags)) return $this->tags;

		$resource = $request->route()->controller->resourceName;

		$this->tags = ['api', $resource];

		if ($request->has('include')) {
			$this->tags = array_merge($this->tags, preg_split('/[.,]+/', $request->get('include')));
		}

		$searchParams = ['query', 'order', 'limit', 'join'];
		foreach ($searchParams as $searchParam) {
			if ($request->has($searchParam)) {
				array_push($this->tags, json_encode($request->get($searchParam)));
			}
		}

		return $this->tags;
	}
}
