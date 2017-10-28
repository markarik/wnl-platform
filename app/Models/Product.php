<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
	protected $fillable = [
		'name',
		'invoice_name',
	];

	protected $guarded = [
		'price',
	];

	protected $dates = [
		'delivery_date',
		'start_date',
		'end_date',
	];

	public function scopeSlug($query, $slug)
	{
		return $query
			->where('slug', $slug)
			->first();
	}

	public function getAvailableAttribute()
	{
		return $this->quantity > 0;
	}
}
