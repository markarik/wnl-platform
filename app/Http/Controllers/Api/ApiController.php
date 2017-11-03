<?php


namespace App\Http\Controllers\Api;

use Auth;
use League\Fractal\Manager;
use Illuminate\Http\Request;
use League\Fractal\Resource\Item;
use App\Http\Controllers\Controller;
use League\Fractal\Resource\Collection;
use App\Http\Controllers\Api\Concerns\TranslatesApiQueries;
use App\Http\Controllers\Api\Serializer\ApiJsonSerializer;
use App\Http\Controllers\Api\Concerns\PerformsApiSearches;
use App\Http\Controllers\Api\Concerns\GeneratesApiResponses;
use App\Http\Controllers\Api\Concerns\ProvidesApiFiltering;

class ApiController extends Controller
{
	use GeneratesApiResponses,
		TranslatesApiQueries,
		PerformsApiSearches,
		ProvidesApiFiltering;

	protected $fractal;
	protected $request;
	public $resourceName;

	public function __construct(Request $request)
	{
		$this->request = $request;
		$this->fractal = new Manager();
		$this->fractal->setRecursionLimit(10);
		$this->fractal->setSerializer(new ApiJsonSerializer());
		if ($this->request->has('include')) {
			$this->fractal->parseIncludes($this->request->get('include'));
			$this->include = $this->request->get('include');
		}
	}

	/**
	 * Get a resource.
	 *
	 * @param $id
	 *
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function get($id)
	{
		$modelName = self::getResourceModel($this->resourceName);
//		$model = $modelName::select();
		$model = $this->eagerLoadIncludes($modelName);
		$transformerName = self::getResourceTransformer($this->resourceName);
		if ($id === 'all') {
			$results = $model->get();
			$resource = new Collection($results, new $transformerName, $this->resourceName);
		} else {
			$results = $model->find($id);
			$resource = new Item($results, new $transformerName, $this->resourceName);
		}
		$data = $this->fractal->createData($resource)->toArray();


//		return view('layouts.app');
		return response()->json($data);
	}

	/**
	 * Delete a resource.
	 *
	 * @param $id
	 *
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function delete($id)
	{
		$modelFullName = self::getResourceModel($this->resourceName);
		$modelName = self::getResourcesStudly($this->resourceName);

		$model = $modelFullName::find($id);

		if (!$model) {
			return $this->respondNotFound();
		}

		if (Auth::user()->can('delete', $model)) {
			$model->forceDelete();

			// self::dispatchRemovedEvent($model, $modelName);

			return $this->respondOk();
		}

		return $this->respondUnauthorized();
	}

	/**
	 * Returns a count of all model's records
	 *
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function count()
	{
		return $this->respondOk([
			'count' => (self::getResourceModel($this->resourceName))::count(),
		]);
	}

	/**
	 * Get resource model class name.
	 *
	 * @param $resource
	 *
	 * @return string
	 */
	public static function getResourceModel($resource)
	{
		return 'App\Models\\' . self::getResourcesStudly($resource);
	}

	/**
	 * Get resource event class name.
	 *
	 * @param $resourceName
	 * @param string $eventName
	 *
	 * @return string
	 */
	protected static function getRemovedResourceEvent($resourceName)
	{
		return 'App\Events\\' . $resourceName . 'Removed';
	}

	/**
	 * Dispatch event.
	 *
	 * @param $resourceName
	 * @param string $eventName
	 *
	 * @return string
	 */
	protected static function dispatchRemovedEvent($model, $resourceName)
	{
		$eventClass = self::getRemovedResourceEvent($resourceName);

		if (class_exists($eventClass)) {
			event(new $eventClass($model, Auth::user()->id, 'deleted'));
		}
	}

	/**
	 * Get resource transformer name.
	 *
	 * @param $resource
	 *
	 * @return string
	 */
	protected static function getResourceTransformer($resource)
	{
		return 'App\Http\Controllers\Api\Transformers\\' . self::getResourcesStudly($resource) . 'Transformer';
	}

	/**
	 * Convert resource name to a class name.
	 *
	 * @param $resource
	 *
	 * @return string
	 */
	protected static function getResourcesStudly($resource)
	{
		return studly_case(str_singular($resource));
	}

	/**
	 * Determine whether a resource should be included.
	 *
	 * @param $name
	 *
	 * @return bool
	 */
	public static function shouldInclude($name)
	{
		return str_is("*{$name}*", \Request::get('include'));
	}

	/**
	 * @param $results
	 *
	 * @return \Illuminate\Http\JsonResponse
	 */
	protected function transformAndRespond($results)
	{
		$data = $this->transform($results);

		return $this->json($data);
	}

	/**
	 * @param $results
	 *
	 * @return array
	 */
	protected function transform($results)
	{
		$transformerName = self::getResourceTransformer($this->resourceName);
		$resource = new Collection($results, new $transformerName, $this->resourceName);

		$data = $this->fractal->createData($resource)->toArray();

		return $data;
	}

	/**
	 * @param $model
	 * @param $limit
	 *
	 * @return array
	 */
	protected function paginatedResponse($model, $limit, $page = 1)
	{
		$paginator = $model->paginate($limit, ['*'], 'page', $page);

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

	protected function eagerLoadIncludes(string $model)
	{
		if (empty($this->include)) return $model::select();
		$relationships = [];

		foreach (explode(',', $this->include) as $chain) {
			$confirmedChain = $this->processChain($chain, $model);
			if ($confirmedChain) {
				array_push($relationships, $confirmedChain);
			}
		}

		return $model::with($relationships);
	}

	protected function modelHasMethod(string $model, string $method)
	{
		return method_exists((new $model), $method);
	}

	protected function processChain(string $chain, string $parentModel)
	{
		$resources = explode('.', $chain);
		$depth = count($resources);
		for ($i = 0; $i < $depth; $i++) {
			if ($i === 0) {
				if (!$this->modelHasMethod($parentModel, $resources[$i])) {
					\Log::debug("Relationship {$resources[$i]} does not exist in model {$parentModel}");
					return false;
				}
			} else {
				$model = self::getResourceModel($resources[$i-1]);
				if (!$this->modelHasMethod($model, $resources[$i])) {
					\Log::debug("Relationship {$resources[$i]} does not exist in model {$model}");
					return false;
				}
			}
		}

		return $chain;
	}
}
