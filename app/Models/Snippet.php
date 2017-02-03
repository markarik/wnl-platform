<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Snippet extends Model
{
    protected $fillable = ['content'];

	public function slides() {
		return $this->morphToMany('\App\Models\Slide', 'presentables');
	}
}
