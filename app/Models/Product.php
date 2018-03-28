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
		'course_start',
		'course_end',
		'access_start',
		'access_end',
		'signups_start',
		'signups_end',
	];

	public function lessons()
	{
		return
			$this->belongsToMany('App\Models\Lesson')
				->withPivot('start_date')
				->using('App\Models\LessonProduct');
	}

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
