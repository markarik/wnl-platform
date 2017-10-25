<?php


namespace App\Http\Controllers\Api;

use Auth;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
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
use App\Http\Controllers\Api\Concerns\PaginatesResponses;

class ApiController extends Controller
{
	use GeneratesApiResponses,
		TranslatesApiQueries,
		PerformsApiSearches,
		ProvidesApiFiltering,
		PaginatesResponses;

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
		$request = $this->request;
		$modelName = self::getResourceModel($this->resourceName);

		if ($id === 'all') {
			$models = $modelName::select();
		} else {
			$models = $modelName::find($id);
		}

		if ($id === 'all' && $request->limit) {
			$data = $this->paginatedResponse($models, $request->limit, $request->page ?? 1);
		} else {
			$data = $this->transform($models);
		}

		return $this->respondOk($data);
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
	 * @param $data
	 *
	 * @return array
	 */
	protected function transform($data)
	{
		$transformerName = self::getResourceTransformer($this->resourceName);
		if ($data instanceof Model) {
			$resource = new Item($data, new $transformerName, $this->resourceName);
		} else {
			if ($data instanceof Builder) {
				$data = $data->get();
			}
			$resource = new Collection($data, new $transformerName, $this->resourceName);
		}

		$data = $this->fractal->createData($resource)->toArray();

		return $data;
	}
}
