<?php namespace App\Http\Controllers\Api\Filters\Quiz;


use App\Http\Controllers\Api\Filters\ApiFilter;
use App\Models\UserPlan;
use Carbon\Carbon;
use Auth;

class PlannedFilter extends ApiFilter
{
	protected $expected = ['user_id', 'list'];

	public function handle($builder)
	{
		$plan = UserPlan::where('user_id', $this->params['user_id'])->orderBy('id', 'desc')->first();

		if (!$plan) return $builder;

		$builder = $builder->where(function ($query) use ($plan){
			foreach ($this->params['list'] as $state) {
				if (!empty($state)) {
					$query->orWhere(function ($query) use ($state, $plan) {
						$this->{$state}($plan, $query);
					});
				}
			}
		});

		return $builder;
	}

	public function count($builder)
	{
		$plan = UserPlan::where('user_id', Auth::user()->id)->orderBy('id', 'desc')->first();
		$all = 0;
		$today = 0;

		if ($plan) {
			$all = $this->all($plan, $builder)->count();
			$today = $this->planned($plan, $builder)->count();
		}

		return [
			'items'   => [
				'planned' =>
					[
						'value' => 'planned',
						'count' => $today,
					],
				'all'     =>
					[
						'value' => 'all',
						'count' => $all,
					],
			],
			'message' => 'planned',
			'type'    => 'list',
			'is_user_specific' => true
		];
	}

	protected function all($plan, $builder)
	{
		$questions = $plan->questionsProgress->pluck('question_id')->toArray();

		return $builder->whereIn('id', $questions);
	}

	protected function planned($plan, $builder)
	{
		$supportedDate = Carbon::today();

		$questionsForDay = $plan->questionsForDay($supportedDate)->pluck('question_id')->toArray();

		return $builder->whereIn('id', $questionsForDay);
	}
}
