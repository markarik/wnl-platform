<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
	protected $fillable = ['name', 'parent_id'];

	public function slides() {
		return $this->morphToMany('\App\Models\Slide', 'presentable');
	}
}
