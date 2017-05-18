<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
	protected $fillable = ['text', 'user_id'];

	public function user()
	{
		return $this->belongsTo('App\Models\User');
	}

	public function quiz_questions()
	{
		return $this->morphedByMany('App\Models\QuizQuestion', 'commentable');
	}

	public function slides()
	{
		return $this->morphedByMany('App\Models\Slide', 'commentable');
	}

	public function qnaAnswers()
	{
		return $this->morphedByMany('App\Models\QnaAnswers', 'commentable');
	}
}
