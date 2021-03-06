<?php

namespace App\Models;

use App\Models\Contracts\WithSlides;
use Illuminate\Database\Eloquent\Model;

class Category extends Model implements WithSlides
{
	protected $fillable = ['name', 'parent_id'];

	public function slides() {
		return $this->morphToMany('\App\Models\Slide', 'presentable')->orderBy('order_number');
	}
}
