<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Answer extends Model
{
	protected $fillable = ['text', 'user_id', 'question_id'];

	public function question()
	{
		return $this->belongsTo('App\Models\Question');
	}

	public function user()
	{
		return $this->belongsTo('App\Models\User');
	}
}
