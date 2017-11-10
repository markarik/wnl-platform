<?php

namespace App\Models\Concerns;


use App\Observers\CachedModelObserver;

trait Cached
{
	public static function bootCached()
	{
		static::observe(new CachedModelObserver);
	}
}
