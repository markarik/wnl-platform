<?php namespace App\Http\Controllers\Api\Concerns;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;

trait TranslatesApiQueries
{
	/**
	 * Generates JSON response based on the query params.
	 *
	 * @param Request $request
	 *
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function query(Request $request)
	{
		$modelName = self::getResourceModel($this->resourceName);
		$model = $this->eagerLoadIncludes($modelName);

		$model = $this->applyFilters($model, $request);

		try {
			if ($request->limit && !is_array($request->limit)) {
				$data = $this->paginatedResponse($model, $request->limit, $request->page ?? 1);
			} else {
				$data = array_filter($this->transform($model), function($value) {
					return !empty($value);
				});
			}
		}
		catch (QueryException $e) {
			\Log::error($e);

			return $this->respondInvalidInput();
		}

		return $this->respondOk($data);
	}

	/**
	 * Parse order rules and apply to query builder.
	 *
	 * @param Model $model
	 * @param array $rules
	 *
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
	 * @param Model $model
	 * @param Request $request
	 *
	 * @return mixed
	 */
	protected function applyFilters($model, $request)
	{
		$order = $request->get('order');
		$limit = $request->get('limit');

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
