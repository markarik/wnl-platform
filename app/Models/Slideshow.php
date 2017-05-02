<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Slideshow extends Model
{
	public function slides()
	{
		return $this->morphToMany('\App\Models\Slide', 'presentable');
	}
}
