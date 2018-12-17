<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use ScoutEngines\Elasticsearch\Searchable;

class Tag extends Model
{
	use Searchable;

	protected $fillable = ['name'];

	protected $touches = ['questions'];

	public function questions()
	{
		return $this->morphedByMany('App\Models\QnaQuestion', 'taggable');
	}

	public function lessons()
	{
		return $this->morphedByMany('App\Models\Lesson', 'taggable');
	}

	public function taggables()
	{
		return $this->hasMany('App\Models\Taggable');
	}
}
