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
		'user_id',
		'filters',
	];

	protected $dates = [
		'start_date',
		'end_date',
		'created_at',
	];

	protected $casts = [
		'filters' => 'array',
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

	public function daysLeftFromDate($date)
	{
		$startDate = $date->lt($this->start_date) ? $this->start_date : $date;

		return $this->end_date->diff($startDate)->days - $this->slack_days_left + 1; // 2
	}

	public function remainingQuestionsFromDate($date)
	{
		$result = $this->questionsProgress()
			->where(function ($query) use ($date) {
				$query
					->where('resolved_at', null)
					->orWhereDate('resolved_at', '>', $date->toDateString());
			})
			->get();

		return $result;
	}

	public function questionsForDay($date)
	{
		$remainingQuestions = $this->remainingQuestionsFromDate($date);
		$daysLeft = $this->daysLeftFromDate($date);

		if (empty($remainingQuestions) || $daysLeft <= 0) {
			return collect();
		}

		$questionsPerDay = ceil($remainingQuestions->count() / $daysLeft); // 500
		$todaysSolved = $this->questionsProgress()
			->whereDate('resolved_at', $date->toDateString())
			->count();

		if ($questionsPerDay - $todaysSolved <= 0 ) {
			return collect();
		}

		$todaysQuestions = $remainingQuestions
			->sortBy('id')
			->take($questionsPerDay - $todaysSolved);

		return $todaysQuestions;
	}

	public function getStatsAttribute()
	{
		$total = $this->questionsProgress()->count();
		$remaining = $this->questionsProgress()
			->where('resolved_at', null)
			->count();
		$done = $total - $remaining;
		$doneToday = $this->questionsProgress()
			->whereDate('resolved_at', Carbon::today())
			->count();

		// TODO: Group resolved_at by day
		$stats = [
			'done'       => $done,
			'remaining'  => $remaining,
			'total'      => $total,
			'done_today' => $doneToday,
		];

		return $stats;
	}

	public function calculatedStartDate() {
		$firstQuestionSolved = $this->questionsProgress()
			->whereNotNull('resolved_at')
			->orderBy('resolved_at')
			->get()
			->first();

		if (!empty($firstQuestionSolved)) {
			return $this->start_date->min(new Carbon($firstQuestionSolved->resolved_at));
		} else {
			return $this->start_date;
		}
	}
}
