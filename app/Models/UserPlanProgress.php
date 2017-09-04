<?php

namespace App\Models;

use Laravel\Scout\Searchable;
use Facades\Lib\Bethink\Bethink;
use Illuminate\Database\Eloquent\Model;

class UserPlanProgress extends Model
{
	protected $fillable = [
		'question_id',
		'user_id',
		'resolved_at',
		'plan_id'
	];

	protected $table = 'users_plan_progress';

	public $timestamps = false;

	public function user()
	{
		return $this->belongsTo('App\Models\User');
	}

	public function plan()
	{
		return $this->belongsTo('App\Models\UserPlan');
	}
}
