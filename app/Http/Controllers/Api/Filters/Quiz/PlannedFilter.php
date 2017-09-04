<?php namespace App\Http\Controllers\Api\Filters\Quiz;


use App\Http\Controllers\Api\Filters\ApiFilter;
use App\Models\QuizQuestion;
use App\Models\UserPlan;
use Carbon\Carbon;
use Auth;

class PlannedFilter extends ApiFilter
{
	protected $expected = ['user_id'];

	public function handle($builder)
	{
		$supportedDate = Carbon::today();

		$plan = UserPlan::where('user_id', $this->params['user_id'])->orderBy('id', 'desc')->first();

		if (!$plan) return $builder;

		$questionsForDay = $plan->questionsForDay($supportedDate)->pluck('question_id')->toArray();

		return $builder->whereIn('id', $questionsForDay);
	}

	public function count($builder)
	{
		$plan = UserPlan::where('user_id', Auth::user()->id)->orderBy('id', 'desc')->first();

		if ($plan) {
			$questionsForDay = $plan->questionsForDay(Carbon::today())->pluck('question_id')->toArray();
			$count = $builder->whereIn('id', $questionsForDay)->count();
		} else {
			$count = 0;
		}

		return [
			'items'   => [
				[
					'value' => 'planned',
					'count' => $count,
				]
			],
			'message' => 'planned',
			'type'    => 'list',
		];
	}
}
