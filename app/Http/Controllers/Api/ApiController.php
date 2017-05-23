<?php


namespace App\Http\Controllers\Api;

use Auth;
use League\Fractal\Manager;
use Illuminate\Http\Request;
use League\Fractal\Resource\Item;
use App\Http\Controllers\Controller;
use League\Fractal\Resource\Collection;
use App\Http\Controllers\Api\Serializer\ApiJsonSerializer;
use App\Http\Controllers\Api\Concerns\PerformsApiSearches;
use App\Http\Controllers\Api\Concerns\GeneratesApiResponses;

abstract class ApiController extends Controller
{
	use GeneratesApiResponses;
	use PerformsApiSearches;

	protected $fractal;
	protected $request;
	protected $resourceName;

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
	 * Get resource model class name.
	 *
	 * @param $resource
	 * @return string
	 */
	protected static function getResourceModel($resource)
	{
		return 'App\Models\\' . self::getResourcesStudly($resource);
	}

	/**
	 * Get resource transformer name.
	 *
	 * @param $resource
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
	 * @return string
	 */
	protected static function getResourcesStudly($resource)
	{
		return studly_case(str_singular($resource));
	}
}
