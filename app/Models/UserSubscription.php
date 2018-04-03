<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class UserSubscription extends Model
{
	const SUBSCRIPTION_DATES_CACHE_KEY = '%s-%s-subscription-dates';
	const CACHE_VER = '1';
	protected $table = 'user_subscription';

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
		'access_start', 'access_end', 'user_id'
	];

	/**
	 * Relationships
	 */
	public function user()
	{
		return $this->hasOne('App\Models\User');
	}
}
