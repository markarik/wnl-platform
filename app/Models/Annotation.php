<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Annotation extends Model
{
	protected $fillable = ['keyword', 'description'];

	public function tags()
	{
		return $this->morphToMany('App\Models\Tag', 'taggable');
	}
}
