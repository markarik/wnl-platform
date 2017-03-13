<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Screen extends Model
{
	protected $fillable = ['content', 'type', 'name'];

	public function slides() {
		return $this->morphToMany('\App\Models\Slide', 'presentable');
	}

	public function lesson(){
		return $this->belongsTo('\App\Models\Lesson');
	}

	public function sections()
	{
		return $this->hasMany('\App\Models\Section');
	}
}
