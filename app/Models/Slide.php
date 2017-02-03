<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Slide extends Model
{
    protected $fillable = ['content', 'is_functional'];

	protected $casts = [
		'is_functional'	=> 'bool',
	];

	public function subjects() {
		return $this->morphedByMany('\App\Models\Subject', 'presentable');
	}

	public function slideShows() {
		return $this->morphedByMany('\App\Models\Snippet', 'presentable');
	}
}
