<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuizQuestion extends Model
{
	protected $fillable = ['text', 'explanation', 'preserve_order'];

	protected $casts = [
		'preserve_order' => 'boolean',
	];

	public function answers()
	{
		return $this->hasMany('App\Models\QuizAnswer');
	}

	public function sets()
	{
		return $this->belongsToMany('App\Models\QuizSet');
	}

	public function comments()
	{
		return $this->morphMany('App\Models\Comment', 'commentable');
	}

	public function tags()
	{
		return $this->morphToMany('App\Models\Tag', 'taggable');
	}
}
