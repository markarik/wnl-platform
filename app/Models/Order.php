<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
	protected $casts = [
		'paid'        => 'boolean',
		'canceled'    => 'boolean',
		'canceled_at' => 'date',
	];

	protected $fillable = [
		'user_id', 'session_id', 'product_id', 'method', 'transfer_title', 'external_id', 'canceled', 'canceled_at',
	];

	protected $guarded = [
		'paid',
	];

	public function scopeRecent($query)
	{
		return $query
			->orderBy('created_at', 'desc')
			->take(1)
			->first();
	}

	public function product()
	{
		return $this->belongsTo('App\Models\Product');
	}

	public function coupon()
	{
		return $this->belongsTo('App\Models\Coupon');
	}

	public function invoices()
	{
		return $this->hasMany('App\Models\Invoice');
	}

	public function user()
	{
		return $this->belongsTo('App\Models\User');
	}

	public function attachCoupon($coupon)
	{
		$this->coupon_id = $coupon->id;
		$this->save();
	}

	public function getTotalWithCouponAttribute()
	{
		$coupon = $this->coupon;

		if (is_null($coupon)) return $this->product->price;

		return $this->product->price - $this->coupon_amount;
	}

	public function getCouponAmountAttribute()
	{
		$coupon = $this->coupon;

		if (is_null($coupon)) return 0;

		if ($coupon->is_percentage) {
			return number_format($coupon->value * $this->product->price / 100, 2);
		}

		return $coupon->value;
	}

	public function cancel()
	{
		$this->canceled = true;
		$this->canceled_at = Carbon::now();
		$this->save();
	}
}
