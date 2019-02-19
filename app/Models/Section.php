<?php

namespace App\Models;

use App\Models\Concerns\Cached;
use App\Scopes\OrderByOrderNumberScope;
use Illuminate\Database\Eloquent\Model;

class Section extends Model
{
	use Cached;

	protected $fillable = ['name', 'screen_id', 'first_slide', 'slides_count', 'order_number'];

	protected static function boot() {
		parent::boot();
		static::addGlobalScope(new OrderByOrderNumberScope());
	}

	public function slides() {
		return $this->morphToMany('\App\Models\Slide', 'presentable');
	}

	public function screen()
	{
		return $this->belongsTo('App\Models\Screen');
	}

	public function subsections()
	{
		return $this->hasMany('App\Models\Subsection');
	}

	public function tags()
	{
		return $this->morphToMany('App\Models\Tag', 'taggable');
	}
}
