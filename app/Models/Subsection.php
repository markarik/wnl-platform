<?php

namespace App\Models;

use App\Models\Concerns\Cached;
use App\Models\Contracts\WithSlides;
use App\Models\Contracts\WithTags;
use Illuminate\Database\Eloquent\Model;

class Subsection extends Model implements WithSlides, WithTags
{
	use Cached;

	protected $fillable = ['name', 'section_id', 'first_slide', 'slides_count', 'order_number'];

	public function slides() {
		return $this->morphToMany('\App\Models\Slide', 'presentable');
	}

	public function section()
	{
		return $this->belongsTo('App\Models\Section');
	}

	public function tags()
	{
		return $this->morphToMany('App\Models\Tag', 'taggable');
	}
}
