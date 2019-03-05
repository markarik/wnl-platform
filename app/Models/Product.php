<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
	protected $fillable = [
		'name',
		'invoice_name',
		'slug',
		'price',
		'quantity',
		'initial',
		'delivery_date',
		'course_start',
		'course_end',
		'access_start',
		'access_end',
		'signups_start',
		'signups_end',
		'signups_close',
		'vat_rate',
		'vat_note',
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
		'signups_close',
	];

	public function lessons()
	{
		return
			$this->belongsToMany('App\Models\Lesson')
				->withPivot('start_date')
				->using('App\Models\LessonProduct');
	}

	public function instalments()
	{
		return $this->hasMany('App\Models\ProductInstalment');
	}

	public function paymentMethods()
	{
		return $this
			->belongsToMany('App\Models\PaymentMethod')
			->withPivot('start_date', 'end_date')
			->using('App\Models\PaymentMethodProduct');
	}

	public function scopeSlug($query, $slug)
	{
		return $query
			->where('slug', $slug)
			->first();
	}

	public function getAvailableAttribute()
	{
		return
			$this->quantity > 0 &&
			$this->signups_start->isPast();
	}
}
