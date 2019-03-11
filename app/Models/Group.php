<?php

namespace App\Models;

use App\Models\Concerns\Cached;
use Illuminate\Database\Eloquent\Model;
use ScoutEngines\Elasticsearch\Searchable;

class Group extends Model
{
	use Cached, Searchable;

	protected $fillable = ['name', 'course_id'];

	public function lessons()
	{
		return	$this->hasMany('\App\Models\Lesson');
	}

	public function course(){
		return $this->belongsTo('\App\Models\Course');
	}
}
