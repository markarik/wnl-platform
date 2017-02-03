<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Slide extends Model
{
    protected $fillable = ['content'];

	public function subjects() {
		return $this->morphedByMany('\App\Models\Subject', 'presentables');
	}

	public function slideShows() {
		return $this->morphedByMany('\App\Models\Snippet', 'presentables');
	}
}
