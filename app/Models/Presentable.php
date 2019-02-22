<?php

namespace App\Models;

use App\Scopes\OrderByOrderNumberScope;
use Illuminate\Database\Eloquent\Model;

class Presentable extends Model
{
	protected static function boot() {
		parent::boot();
		static::addGlobalScope(new OrderByOrderNumberScope());
	}
}
