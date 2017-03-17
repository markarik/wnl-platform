<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
	protected $fillable = ['name'];

	public function questions()
	{
		return $this->morphedByMany('App\Models\Question', 'taggable');
	}

	public function lessons()
	{
		return $this->morphedByMany('App\Models\Lesson', 'taggable');
	}
}
