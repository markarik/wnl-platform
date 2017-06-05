<?php

namespace App\Models\Concerns;


use App\Observers\CachedModelObserver;

trait Cached
{
	public static function boot()
	{
		parent::boot();
		static::observe(new CachedModelObserver);
	}
}
