<?php

namespace App\Http\Middleware;

use Cache;
use Closure;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
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
	 *
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
			return $this->handleResponse($request, $cached);
		}

		$response = $next($request);

		if ($this->responseValid($response)) {
			$data = $this->getData($response);
			Cache::tags($tags)->put($key, $data, 60 * 24);
		}

		return $response;
	}

	protected function handleResponse($request, $data)
	{
		if ($request->expectsJson()) {
			return $this->respondOk($data);
		}

		return response($data);
	}

	protected function getData($response)
	{
		if ($response instanceof JsonResponse) {
			return $response->getData();
		}

		return $response->getContent();
	}

	protected function responseValid($response)
	{
		return
			$response instanceof Response &&
			$response->getStatusCode() === 200;
	}

	protected function excluded($request)
	{
		$excludedTags = ['users', 'profiles', 'reactions', 'orders',
			'state', 'quiz_stats', 'notifications', 'user_plan', 'quiz_results'];

		$methodExcluded = !in_array($request->method(), ['GET', 'POST']);
		$queryExcluded = (bool)array_intersect($excludedTags, $this->getTags($request));
		$postExcluded = $request->method() === 'POST' && !str_is('*.search*', $request->getRequestUri());
		$quizStats = str_is('*quiz_questions/stats*', $request->getRequestUri());

		return
			$methodExcluded ||
			$queryExcluded ||
			$postExcluded ||
			$quizStats;
	}

	protected function getTags($request)
	{
		if (!empty($this->tags)) return $this->tags;

		$resource = $this->getResource($request);

		$this->tags = ['api', $resource];

		if ($request->has('include')) {
			$this->tags = array_merge($this->tags, preg_split('/[.,]+/', $request->get('include')));
		}

		if ($request->method() === 'GET' && str_is('*.search*', $request->getRequestUri()) && $request->has('q')) {
			$this->tags[] = 'search';
		}

		$searchParams = ['query', 'order', 'limit', 'join'];
		foreach ($searchParams as $searchParam) {
			if ($request->has($searchParam)) {
				array_push($this->tags, json_encode($request->get($searchParam)));
			}
		}

		if (str_is('*current*', $request->getRequestUri())) {
			array_push($this->tags, 'user-' . \Auth::user()->id);
		}

		return $this->tags;
	}

	/**
	 * @param $request
	 *
	 * @return mixed
	 */
	protected function getResource($request)
	{
		if ($request->route('resource') !== null) {
			return $request->route('resource');
		}

		return $request->route()->controller->resourceName;
	}
}
