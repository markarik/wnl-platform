<?php

namespace App\Models;

use App\Models\Concerns\Cached;
use Illuminate\Database\Eloquent\Model;

class Edition extends Model
{
	use Cached;

	protected $fillable = ['course_id', 'name', 'start_date'];

	public function course()
	{
		return $this->belongsTo('App\Models\Course');
	}
}
