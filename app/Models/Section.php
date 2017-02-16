<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Section extends Model
{
	protected $fillable = ['name', 'snippet_id'];

	public function slides() {
		return $this->morphToMany('\App\Models\Slide', 'presentable');
	}
}
