<?php namespace App\Http\Controllers\Api\Concerns;

use Illuminate\Http\Request;
use League\Fractal\Resource\Collection;
use Illuminate\Database\QueryException;

trait PerformsApiSearches
{

	/**
	 * Generates JSON response based on the search params.
	 *
	 * @param Request $request
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function search(Request $request)
	{
		$modelName = self::getResourceModel($this->resourceName);
		$model = new $modelName;

		$model = $this->applyFilters($model, $request);

		try {
			$results = $model->get();
		}
		catch (QueryException $e) {
			\Log::error($e);

			return $this->respondInvalidInput();
		}

		$transformerName = self::getResourceTransformer($this->resourceName);
		$resource = new Collection($results, new $transformerName, $this->resourceName);

		$data = $this->fractal->createData($resource)->toArray();

		return $this->respondOk($data);
	}

	/**
	 * Process 'whereHas' conditions and apply to query builder.
	 *
	 * @param $model
	 * @param $relationConditions
	 * @return mixed
	 */
	protected function parseWhereHas($model, $relationConditions)
	{
		foreach ($relationConditions as $field => $conditions) {
			$model = $model->whereHas(camel_case($field),
				function ($query) use ($conditions) {
					$query->where($conditions['where']);
				}
			);
		}

		return $model;
	}

	/**
	 * Parse order rules and apply to query builder.
	 *
	 * @param $model
	 * @param $rules
	 * @return mixed
	 */
	protected function parseOrder($model, $rules)
	{
		foreach ($rules as $field => $order) {
			$model = $model->orderBy($field, $order);
		}

		return $model;
	}

	/**
	 * Apply filters from request to the model.
	 *
	 * @param $model
	 * @param $request
	 * @return mixed
	 */
	protected function applyFilters($model, $request)
	{
		$query = $request->get('query');
		$order = $request->get('order');
		$limit = $request->get('limit');

		if (!empty ($query['where'])) {
			$model = $model->where($query['where']);
		}

		if (!empty ($query['whereHas'])) {
			$model = $this->parseWhereHas($model, $query['whereHas']);
		}

		if (!empty ($order)) {
			$model = $this->parseOrder($model, $order);
		}

		if (!empty ($limit)) {
			list ($limit, $offset) = $limit;
			$model = $model
				->offset($offset)
				->limit($limit);
		}

		return $model;
	}

}
