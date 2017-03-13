<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Edition extends Model
{
	protected $fillable = ['course_id', 'name', 'start_date'];

	public function course()
	{
		return $this->belongsTo('App\Models\Course');
	}
}
