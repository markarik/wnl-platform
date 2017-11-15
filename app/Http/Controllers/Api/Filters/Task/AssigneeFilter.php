<?php namespace App\Http\Controllers\Api\Filters\Task;

use App\Http\Controllers\Api\Filters\ApiFilter;
use App\Models\Task;
use Carbon\Carbon;
use Auth;

class AssigneeFilter extends ApiFilter
{
	protected $expected = ['user_id'];

	public function handle($builder)
	{
        $userId = $this->params['user_id'];
        return $builder->where('assignee_id', $userId);
	}

	public function count($builder)
	{
		return null;
	}
}
