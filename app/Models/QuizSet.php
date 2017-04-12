<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuizSet extends Model
{
	protected $fillable = ['name'];

	public function questions()
	{
		return $this->belongsToMany('App\Models\QuizQuestion');
	}
}
