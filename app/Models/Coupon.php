<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
	protected $fillable = ['name', 'slug', 'code', 'type', 'value', 'expires_at'];

	public function getIsPercentageAttribute()
	{
		return $this->type === 'percentage';
	}

	public function scopeSlug($query, $slug)
	{
		return $query
			->where('slug', $slug)
			->first();
	}
}
