<?php

namespace App\Models;

use App\Models\Concerns\Cached;
use App\Models\Contracts\WithSlides;
use Facades\Lib\Bethink\Bethink;
use Illuminate\Database\Eloquent\Model;

class Slideshow extends Model implements WithSlides
{
	use Cached;

	protected $fillable = ['background'];

	protected $appends = ['background_url'];

	public function slides()
	{
		return $this->morphToMany('\App\Models\Slide', 'presentable')->orderBy('order_number');
	}

	public function getBackgroundUrlAttribute()
	{
		if (is_null($this->background)) return null;
		return Bethink::getAssetPublicUrl("backgrounds/{$this->background}");
	}
}
