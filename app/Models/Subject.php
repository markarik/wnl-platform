<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
	protected $fillable = ['name'];

	public function slides() {
		return $this->morphToMany('\App\Models\Slide', 'presentable');
	}

	public function sections() {
		return $this->hasMany('\App\Models\Section');
	}

	public function lessons() {
		return $this->hasMany('\App\Models\Lesson');
	}

	public function screens() {
		return $this->hasManyThrough('\App\Models\Screen', '\App\Models\Lesson');
	}
}
