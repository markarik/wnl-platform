<?php

namespace App\Models;

use App\Events\Qna\AnswerPosted;
use Illuminate\Database\Eloquent\Model;

class QnaAnswer extends Model
{
	protected $fillable = ['text', 'user_id', 'question_id'];

	protected $events = [
		'created' => AnswerPosted::class,
	];

	public function question()
	{
		return $this->belongsTo('App\Models\QnaQuestion');
	}

	public function user()
	{
		return $this->belongsTo('App\Models\User');
	}

	public function comments()
	{
		return $this->morphMany('App\Models\Comment', 'commentable');
	}

	public function reactions()
	{
		return $this->morphToMany('App\Models\Reaction', 'reactable');
	}
}
