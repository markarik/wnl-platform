<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Slideshow extends Model
{
	protected $fillable = ['background'];

	protected $appends = ['background_url'];

	public function slides()
	{
		return $this->morphToMany('\App\Models\Slide', 'presentable');
	}

	public function getBackgroundUrlAttribute()
	{
		return asset("storage/backgrounds/{$this->background}");
	}
}
