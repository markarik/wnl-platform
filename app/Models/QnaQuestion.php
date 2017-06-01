<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QnaQuestion extends Model
{
	protected $fillable = ['text', 'user_id'];

	public function answers()
	{
		return $this->hasMany('App\Models\QnaAnswer', 'question_id');
	}

	public function user()
	{
		return $this->belongsTo('App\Models\User');
	}

	public function tags()
	{
		return $this->morphToMany('App\Models\Tag', 'taggable');
	}

	public function reactions()
	{
		return $this->morphToMany('App\Models\Reaction', 'reactable');
	}

	public function getLessonsAttribute()
	{
		return Lesson::whereHas('tags', function ($query) {
			$query->whereIn('tags.id', $this->tags->keyBy('id')->keys());
		})->get();
	}
}
