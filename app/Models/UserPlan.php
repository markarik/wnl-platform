<?php

namespace App\Models;

use Laravel\Scout\Searchable;
use Facades\Lib\Bethink\Bethink;
use Illuminate\Database\Eloquent\Model;

class UserPlan extends Model
{
	protected $fillable = [
		'start_date',
		'end_date',
		'slack_days_planned',
        'slack_days_left',
        'user_id'
    ];

    protected $table = 'users_plans';

    public $timestamps = false;

	public function user()
	{
		return $this->belongsTo('App\Models\User');
	}

	public function questionsProgress()
	{
		return $this->hasMany('App\Models\UsersPlanProgress');
	}
}
