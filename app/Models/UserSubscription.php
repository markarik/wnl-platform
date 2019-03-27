<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class UserSubscription extends Model
{
	const CACHE_VER = '3';
	const SUBSCRIPTION_DATES_CACHE_KEY = '%s-%s-subscription-dates';
	const SUBSCRIPTION_STATUS_ACTIVE = 'active';
	const SUBSCRIPTION_STATUS_AWAITING = 'awaiting';
	const SUBSCRIPTION_STATUS_INACTIVE = 'inactive';

	protected $table = 'user_subscription';

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
		'access_start', 'access_end', 'user_id'
	];

	protected $dates = ['access_start', 'access_end'];

	/**
	 * Relationships
	 */
	public function user()
	{
		return $this->belongsTo('App\Models\User');
	}

	public function getSubscriptionStatusAttribute()
	{
		$key = self::getCacheKey($this->user->id);

		return \Cache::remember($key, 60 * 24, function () {
			$dates = $this->getSubscriptionDates();

			return $this->getSubscriptionStatus($dates);
		});
	}

	public function getSubscriptionDatesAttribute()
	{
		list ($min, $max) = $this->getSubscriptionDates();

		return [
			'min' => $min->timestamp ?? null,
			'max' => $max->timestamp ?? null,
		];
	}

	/**
	 * @param Carbon[]|array $dates
	 * @return string
	 */
	protected function getSubscriptionStatus($dates)
	{
		if ($this->user->hasRole([Role::ROLE_ADMIN, Role::ROLE_MODERATOR, Role::ROLE_TEST])) {
			return self::SUBSCRIPTION_STATUS_ACTIVE;
		}

		list ($min, $max) = $dates;

		if (empty($min) || empty($max)) {
			return self::SUBSCRIPTION_STATUS_INACTIVE;
		}

		if ($min->isPast() && $max->isFuture()) return self::SUBSCRIPTION_STATUS_ACTIVE;
		if ($min->isFuture() && $max->isFuture()) return self::SUBSCRIPTION_STATUS_AWAITING;

		return self::SUBSCRIPTION_STATUS_INACTIVE;
	}

	protected function getSubscriptionDates()
	{
		$min = Carbon::parse($this->access_start);
		$max = Carbon::parse($this->access_end);

		return [$min, $max];
	}

	public static function getCacheKey($id)
	{
		return sprintf(self::SUBSCRIPTION_DATES_CACHE_KEY, self::CACHE_VER, $id);
	}
}
