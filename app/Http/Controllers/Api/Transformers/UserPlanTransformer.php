<?php


namespace App\Http\Controllers\Api\Transformers;


use App\Http\Controllers\Api\ApiTransformer;
use App\Models\UserPlan;


class UserPlanTransformer extends ApiTransformer
{
	protected $parent;

	public function __construct($parent = null)
	{
		$this->parent = $parent;
	}

	public function transform(UserPlan $userPlan)
	{
		$data = [
			'user_id'               => $userPlan->user_id,
			'start_date'            => $userPlan->start_date,
			'calculated_start_date' => $userPlan->calculatedStartDate(),
			'end_date'              => $userPlan->end_date,
			'slack_days_planned'    => $userPlan->slack_days_planned,
			'slack_days_left'       => $userPlan->slack_days_left,
			'stats'                 => $userPlan->stats,
			'id'                    => $userPlan->id
		];

		if ($this->parent) {
			$data = array_merge($data, $this->parent);
		}

		return $data;
	}
}
