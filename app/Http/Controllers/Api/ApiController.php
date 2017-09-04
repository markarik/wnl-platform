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
		$transformerName = self::getResourceTransformer($this->resourceName);
		if ($id === 'all') {
			$models = $modelName::all();
			$resource = new Collection($models, new $transformerName, $this->resourceName);
		} else {
			$models = $modelName::find($id);
			$resource = new Item($models, new $transformerName, $this->resourceName);
		}
		$data = $this->fractal->createData($resource)->toArray();

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
		$modelName = self::getResourceModel($this->resourceName);

		$model = $modelName::find($id);

		if (!$model) {
			return $this->respondNotFound();
		}

		if (Auth::user()->can('delete', $model)) {
			$model::destroy($id);

			return $this->respondOk();
		}

		return $this->respondUnauthorized();
	}

	/**
	 * Returns a count of all model's records
	 *
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function count() {
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
}
