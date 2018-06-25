<?php

namespace App\Http\Middleware;

use App\Http\Controllers\Api\Concerns\GeneratesApiResponses;
use Cache;
use Closure;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

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

		\Log::debug('Api cache tags: ' . implode(',', $tags));

		if ($this->excluded($request)) {
			\Log::debug('Request excluded from api cache ' . $key);
			return $next($request);
		}

		$cached = Cache::tags($tags)->get($key);

		if ($cached !== null) {
			\Log::debug('Loading response from cache ' . $key);

			return $this->handleResponse($request, $cached);
		}

		\Log::debug('Cache has no response for ' . $key);
		$response = $next($request);

		if ($this->responseValid($response)) {
			$data = $this->getData($response);
			Cache::tags($tags)->put($key, $data, 60 * 24);
		}

		return $response;
	}

	protected function handleResponse($request, $data)
	{
		$decoded = json_decode($data, true);
		if ($decoded !== null) {
			return $this->respondOk($decoded);
		}

		return response($data);
	}

	protected function getData($response)
	{
		if ($response instanceof JsonResponse) {
			return json_encode($response->getData(), JSON_UNESCAPED_SLASHES);
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
			'state', 'quiz_stats', 'notifications', 'user_plan',
			'quiz_results', 'tasks', 'reactables', 'search', 'user_profiles', 'invoices', 'certificates'];

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

		if ($request->method() === 'GET' &&
			str_is('*.search*', $request->getRequestUri()) &&
			$request->has('q')
		) {
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
