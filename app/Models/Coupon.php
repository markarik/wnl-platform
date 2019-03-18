<?php

namespace App\Models;

use App\Events\Coupons\CouponCreated;
use App\Events\Coupons\CouponDeleted;
use App\Events\Coupons\CouponUpdated;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
	protected $fillable = ['name', 'slug', 'code', 'type', 'value', 'expires_at', 'user_id', 'times_usable', 'kind'];

	protected $dates = [
		'expires_at',
	];

	const KIND_GROUP = 'group';
	const KIND_STUDY_BUDDY = 'study_buddy';
	const KIND_VOUCHER = 'voucher';
	const KIND_PARTICIPANT = 'participant';
	const SLUG_WNL_ONLINE_ONLY = 'wnl-online-only';

	protected $dispatchesEvents = [
		'created' => CouponCreated::class,
		'updated' => CouponUpdated::class,
		'deleted' => CouponDeleted::class
	];

	public function studyBuddy()
	{
		return $this->hasOne('App\Models\StudyBuddy', 'code', 'code');
	}

	public function products()
	{
		return $this->belongsToMany('App\Models\Product');
	}

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

	public function scopeValidCode($query, $code)
	{
		return $query
			->where('code', $code)
			->where(function ($query) {
				$query
					->where('times_usable', '>', 0)
					->orWhere('times_usable', null);
			})
			->where('expires_at', '>', Carbon::now())
			->first();
	}

	public function getValueWithUnitAttribute()
	{
		return trans($this->is_percentage ? 'common.percent' : 'common.currency', [
			'value' => $this->value,
		]);
	}
}
