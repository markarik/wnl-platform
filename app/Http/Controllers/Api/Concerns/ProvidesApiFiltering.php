<?php namespace App\Http\Controllers\Api\Concerns;

use App\Exceptions\ApiFilterException;
use Illuminate\Http\Request;

trait ProvidesApiFiltering
{
	public $defaultLimit = 30;

	public $limit;

	public function filter(Request $request)
	{
		$resource = $request->route('resource');
		$model = app(static::getResourceModel($resource));
		$this->limit = $request->limit ?? $this->defaultLimit;
		$randomize = $request->randomize;

		$model = $this->addFilters($request, $model);

		if (!empty($randomize)) {
			$response = $this->randomizedResponse($model, $this->limit);
		} else {
			$response = $this->paginatedResponse($model, $this->limit);
		}

		return $this->respondOk($response);
	}

	private function addFilters($request, $model)
	{
		if (empty($request->filters)) {
			return $model;
		}

		foreach ($request->filters as $filter) {
			$filter = $this->getFilter($filter);
			$model = $filter->apply($model);
		}

		return $model;
	}

	private function getFilter($filter)
	{
		$this->checkIsArray($filter);
		$filterName = array_keys($filter)[0];
		$params = $filter[$filterName];
		$this->checkIsArray($params);
		$filterClass = $this->getFilterClass($filterName);

		return new $filterClass($params);
	}

	private function getFilterClass($filterName)
	{
		$className = collect(explode('.', $filterName))
			->map(function ($v) {
				return studly_case($v);
			})
			->implode('\\') . 'Filter';

		return 'App\Http\Controllers\Api\Filters\\' . $className;
	}

	private function checkIsArray($filter)
	{
		if (is_array($filter)) return;

		throw new ApiFilterException('Filter must be an array of arrays.');
	}

	private function randomizedResponse($model, $limit) {
		$randomQuestions = $model->inRandomOrder()
		->limit($limit)
		->get();

		return [
			'data' => $randomQuestions
		];
	}
}
