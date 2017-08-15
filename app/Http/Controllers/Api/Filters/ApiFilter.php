<?php namespace App\Http\Controllers\Api\Filters;


use App\Exceptions\ApiFilterException;
use Illuminate\Http\Request;

abstract class ApiFilter
{
	/**
	 * @var Request
	 */
	protected $request;

	/**
	 * Array of params the filter expects to receive.
	 *
	 * @var array
	 */
	protected $expected;

	/**
	 * @param array $params
	 */
	public function __construct(array $params)
	{
		$this->request = app(Request::class);
		$this->params = $params;
		$this->checkFilterParams();
	}

	/**
	 * Apply filter to model.
	 *
	 * @param $model
	 */
	public abstract function apply($model);

	/**
	 * Make sure filter has received all information it requires.
	 * Override this method for filters without params.
	 */
	protected function checkFilterParams()
	{
		if (!$this->expected){
			throw new ApiFilterException(static::class . ' has no expected params.');
		}

		$diff = array_diff($this->expected, array_keys($this->params));

		if ($diff){
			throw new ApiFilterException('Missing filter params: ' . implode($diff, ','));
		}
	}
}
