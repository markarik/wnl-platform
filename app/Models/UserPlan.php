<?php

namespace App\Models;

use Laravel\Scout\Searchable;
use Facades\Lib\Bethink\Bethink;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class UserPlan extends Model
{
	protected $fillable = [
		'start_date',
		'end_date',
		'slack_days_planned',
		'slack_days_left',
		'user_id'
	];

	protected $dates = [
		'start_date',
		'end_date',
		'created_at'
	];

	protected $table = 'users_plans';

	public $timestamps = false;

	public function user()
	{
		return $this->belongsTo('App\Models\User');
	}

	public function questionsProgress()
	{
		return $this->hasMany('App\Models\UserPlanProgress', 'plan_id');
	}

	public function daysLeftFromDate($date) {
		$startDate = $date->lt($this->start_date) ? $this->start_date : $date;

		return $this->end_date->diff($startDate)->days - $this->slack_days_left; // 2
	}

	public function remainingQuestionsFromDate($date) {
		return $this->questionsProgress()->where('resolved_at', null)
			->orWhere('resolved_at', '>=', $date->toDateString())->get(); //1001
	}

	public function questionsForDay($date) {
		$remainingQuestions = $this->remainingQuestionsFromDate($date);
		$daysLeft = $this->daysLeftFromDate($date);

		// TODO be smarter!
		if (empty($remainingQuestions) || $daysLeft <= 0) {
			return 0;
		}

		$questionsPerDay = floor($remainingQuestions->count() / $daysLeft); // 500

		$todaysQuestions = $remainingQuestions
			->sortBy('id')
			->take($questionsPerDay)
			->filter(function($value, $key) {
				return empty($value->resolved_at);
			});

		return $todaysQuestions;
	}
}
