<?php namespace App\Http\Controllers\Api\Filters;


use App\Exceptions\ApiFilterException;
use Illuminate\Database\Eloquent\Builder;
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
	 * Filter params.
	 *
	 * @var array
	 */
	protected $params;

	/**
	 * ApiFilter constructor.
	 */
	public function __construct()
	{
		$this->request = app(Request::class);
	}

	/**
	 * Apply filter to the builder.
	 *
	 * @param Builder $builder
	 *
	 * @return Builder
	 */
	protected abstract function handle($builder);

	/**
	 * Perform some common actions on input values and call the
	 * actual filtering method.
	 *
	 * @param Builder $builder
	 * @param array $params
	 *
	 * @return Builder
	 * @throws ApiFilterException
	 */
	public function apply($builder, $params)
	{
		$this->params = $params;
		$this->checkFilterParams();

		return $this->handle($builder);
	}

	/**
	 * Provides possible values that can be fed to the filter.
	 *
	 * @return array
	 */
	public function values()
	{
		return [];
	}

	/**
	 * Provides values list with items count.
	 *
	 * @return array
	 */
	public function count($builder)
	{
		return [];
	}

	/**
	 * Make sure filter has received all information it requires.
	 *
	 * @throws ApiFilterException
	 */
	protected function checkFilterParams()
	{
		if (!$this->expected) return;

		$diff = array_diff($this->expected, array_keys($this->params));

		if ($diff) {
			throw new ApiFilterException('Missing filter params: ' . implode($diff, ','));
		}
	}

	public function setParams($params)
	{
		$this->params = $params;
	}
}
