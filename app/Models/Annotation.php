<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Annotation extends Model
{
	protected $fillable = ['title', 'description'];

	public function keywords()
	{
		return $this->hasMany('\App\Models\Keyword');
	}

	public function tags()
	{
		return $this->morphToMany('App\Models\Tag', 'taggable');
	}
}
