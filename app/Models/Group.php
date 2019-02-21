<?php

namespace App\Models;

use App\Models\Concerns\Cached;
use App\Scopes\OrderByOrderNumberScope;
use Illuminate\Database\Eloquent\Model;
use ScoutEngines\Elasticsearch\Searchable;

class Group extends Model
{
	use Cached, Searchable;

	protected $fillable = ['name', 'course_id'];

	protected static function boot() {
		parent::boot();
		static::addGlobalScope(new OrderByOrderNumberScope());
	}

	public function lessons()
	{
		return	$this->hasMany('\App\Models\Lesson');
	}

	public function course(){
		return $this->belongsTo('\App\Models\Course');
	}
}
