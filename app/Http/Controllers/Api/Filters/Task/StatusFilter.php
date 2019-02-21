<?php namespace App\Http\Controllers\Api\Filters\Task;

use App\Http\Controllers\Api\Filters\ApiFilter;
use App\Models\Task;
use Carbon\Carbon;
use Auth;

class StatusFilter extends ApiFilter
{
	protected $expected = ['included', 'excluded'];

	public function handle($builder)
	{
		if (!empty($this->params['excluded'])) {
			$builder = $builder->whereNotIn('status', $this->params['excluded']);
		}

		if (!empty($this->params['included'])) {
			$builder = $builder->whereIn('status', $this->params['included']);
		}

		return $builder;
	}

	public function count($builder)
	{
		return [];
	}
}
