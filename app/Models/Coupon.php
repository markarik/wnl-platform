<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
	protected $fillable = ['name', 'slug', 'code', 'type', 'value', 'expires_at', 'user_id'];

	protected $dates = [
		'expires_at'
	];

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

	public function getValueWithUnitAttribute()
	{
		return trans($this->is_percentage ? 'common.percent' : 'common.currency', [
			'value' => $this->value
		]);
	}
}
