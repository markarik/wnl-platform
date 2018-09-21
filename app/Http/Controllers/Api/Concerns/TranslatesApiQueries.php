<?php namespace App\Http\Controllers\Api\Concerns;

use Carbon\Carbon;
use Illuminate\Http\Request;
use League\Fractal\Resource\Collection;
use Illuminate\Database\QueryException;

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
	 * Process 'whereHas' conditions and apply to query builder.
	 *
	 * @param $model
	 * @param $relationConditions
	 *
	 * @return mixed
	 */
	protected function parseWhereHas($model, $relationConditions)
	{
		foreach ($relationConditions as $field => $conditions) {
			$model = $model->whereHas(camel_case($field),
				function ($query) use ($conditions) {
					if (!empty($conditions['where'])) {
						$query->where($conditions['where']);
					}
					if (!empty($conditions['whereIn'])) {
						$query->whereIn($conditions['whereIn'][0], $conditions['whereIn'][1]);
					}
				}
			);
		}

		return $model;
	}

	/**
	 * Process 'whereDoesntHave' conditions and apply to query builder.
	 *
	 * @param $model
	 * @param $relationConditions
	 *
	 * @return mixed
	 */
	protected function parseWhereDoesntHave($model, $relationConditions)
	{
		foreach ($relationConditions as $field => $conditions) {
			$model = $model->whereDoesntHave(camel_case($field),
				function ($query) use ($conditions) {
					if (!empty($conditions['where'])) {
						$query->where($conditions['where']);
					}
					if (!empty($conditions['whereIn'])) {
						$query->whereIn($conditions['whereIn'][0], $conditions['whereIn'][1]);
					}
				}
			);
		}

		return $model;
	}

	/**
	 * Process 'hasIn' conditions and apply to query builder.
	 *
	 * @param $model
	 * @param $relationConditions
	 *
	 * @return mixed
	 */
	protected function parseHasIn($model, $relationConditions)
	{
		foreach ($relationConditions as $field => $conditions) {
			foreach ($conditions[1] as $element) {
				$model = $model->whereHas(camel_case($field),
					function ($query) use ($conditions, $element) {
						$query->where($conditions[0], $element);
					}
				);
			}
		}

		return $model;
	}

	/**
	 * Parse order rules and apply to query builder.
	 *
	 * @param $model
	 * @param $rules
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
	 * Handle join statements and apply to query builder.
	 *
	 * @param $model
	 * @param $join
	 *
	 * @return mixed
	 */
	protected function parseJoin($model, $joins)
	{
		foreach ($joins as $join) {
			$model = $model->join($join[0], $join[1], $join[2], $join[3]);
		}

		return $model;
	}

	/**
	 * Apply filters from request to the model.
	 *
	 * @param $model
	 * @param $request
	 *
	 * @return mixed
	 */
	protected function applyFilters($model, $request)
	{
		$query = $this->parseTime($request->get('query'));
		$select = $request->get('select');
		$order = $request->get('order');
		$limit = $request->get('limit');
		$join = $request->get('join');

		if (!empty($select)) {
			$model->select($select);
		}
		$model = $this->parseQuery($model, $query);

		if (!empty ($order)) {
			$model = $this->parseOrder($model, $order);
		}

		if (!empty($join)) {
			$model = $this->parseJoin($model, $join);
		}

		if (!empty ($limit)) {
			list ($limit, $offset) = $limit;
			$model = $model
				->offset($offset)
				->limit($limit);
		}

		return $model;
	}

	protected function parseTime($array)
	{
		// Prezes, pamiętaj, to jest bardzo brzydka szpachla, nie rób tak.
		return array_map(function ($v) {
			if (is_array($v)) return $this->parseTime($v);
			if (str_is('timestamp:*', $v))
				return Carbon::createFromTimestamp(str_replace('timestamp:', '', $v));

			return $v;
		}, $array);
	}

	protected function parseQuery($model, $query)
	{
		if (!empty($query['whereIn'])) {
			$model = $model->whereIn($query['whereIn'][0], $query['whereIn'][1]);
		}

		if (!empty($query['whereInMulti'])) {
			foreach($query['whereInMulti'] as $whereIn) {
				$model = $model->whereIn($whereIn[0], $whereIn[1]);
			}
		}

		if (!empty($query['whereNotIn'])) {
			$model = $model->whereNotIn($query['whereNotIn'][0], $query['whereNotIn'][1]);
		}

		if (!empty($query['whereNull'])) {
			$model = $model->whereNull($query['whereNull'][0]);
		}

		if (!empty ($query['where'])) {
			$model = $model->where($query['where']);
		}

		if (!empty ($query['orWhere'])) {
			$model = $model->orWhere($query['orWhere']);
		}

		if (!empty ($query['whereHas'])) {
			$model = $this->parseWhereHas($model, $query['whereHas']);
		}

		if (!empty ($query['whereDoesntHave'])) {
			$model = $this->parseWhereDoesntHave($model, $query['whereDoesntHave']);
		}

		if (!empty ($query['doesntHave'])) {
			$model = $model->doesntHave($query['doesntHave']);
		}

		if (!empty ($query['hasIn'])) {
			$model = $this->parseHasIn($model, $query['hasIn']);
		}

		return $model;
	}
}
