<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Notifications\Notifiable;
use App\Notifications\ResetPasswordNotification;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
	use Notifiable;

	const SUBSCRIPTION_DATES_CACHE_KEY = '%s-%s-subscription-dates';
	const CACHE_VER = '1';

	protected $casts = [
		'invoice'            => 'boolean',
		'consent_newsletter' => 'boolean',
		'consent_account'    => 'boolean',
		'consent_order'      => 'boolean',
		'consent_terms'      => 'boolean',
		'suspended'          => 'boolean',
	];

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
		'first_name', 'last_name', 'email', 'password', 'address', 'zip', 'city', 'phone', 'invoice',
		'invoice_name', 'invoice_nip', 'invoice_address', 'invoice_zip', 'invoice_city', 'invoice_country',
		'consent_newsletter', 'consent_account', 'consent_order', 'consent_terms',
	];

	/**
	 * The attributes that should be hidden for arrays.
	 *
	 * @var array
	 */
	protected $hidden = [
		'password', 'remember_token',
	];

	protected $guarded = ['suspended'];

	protected $appends = ['subscription_status'];

	/**
	 * Relationships
	 */

	public function orders()
	{
		return $this->hasMany('App\Models\Order');
	}

	public function coupons()
	{
		return $this->hasMany('App\Models\Coupon');
	}

	public function profile()
	{
		return $this->hasOne('App\Models\UserProfile');
	}

	public function billing()
	{
		return $this->hasOne('App\Models\UserBillingData');
	}

	public function settings()
	{
		return $this->hasOne('App\Models\UserSettings');
	}

	public function address()
	{
		return $this->hasOne('App\Models\UserAddress');
	}

	public function roles()
	{
		return $this->belongsToMany('App\Models\Role');
	}

	public function chatMessages()
	{
		return $this->hasMany('App\Models\ChatMessage');
	}

	public function notifications()
	{
		return $this->morphMany('App\Models\Notification', 'notifiable');
	}

	public function sessions()
	{
		return $this->hasMany('App\Models\Session');
	}

	public function comments()
	{
		return $this->hasMany('App\Models\Comment');
	}

	public function tasks()
	{
		return $this->hasMany('App\Models\Task', 'assignee_id');
	}

	public function qnaAnswers()
	{
		return $this->hasMany('App\Models\QnaAnswer');
	}

	public function lessonsAvailability()
	{
		return $this->belongsToMany('App\Models\Lesson', 'user_lesson');
	}

	public function reactables()
	{
		return $this->hasMany('App\Models\Reactable');
	}

	public function chatRooms()
	{
		return $this->belongsToMany('App\Models\ChatRoom');
	}

	/**
	 * Dynamic attributes
	 */

	public function getFullNameAttribute()
	{
		return $this->first_name . ' ' . $this->last_name;
	}

	public function getAddressAttribute($value)
	{
		return decrypt($value);
	}

	public function setAddressAttribute($value)
	{
		$this->attributes['address'] = encrypt($value);
	}

	public function getPhoneAttribute($value)
	{
		return decrypt($value);
	}

	public function setPhoneAttribute($value)
	{
		$this->attributes['phone'] = encrypt($value);
	}

	public function getIsSubscriberAttribute()
	{
		return !is_null(Subscriber::where('email', $this->email)->first());
	}

	public function getSubscriptionStatusAttribute()
	{
		$dates = $this->getSubscriptionDates();
		return $this->getSubscriptionStatus($dates);
	}

	public function getSubscriptionDatesAttribute() {
		list ($min, $max) = $this->getSubscriptionDates();
		return [
			'min' => $min->timestamp,
			'max' => $max->timestamp
		];
	}

	protected function getSubscriptionStatus($dates)
	{
		list ($min, $max) = $dates;

		if ($min->isPast() && $max->isFuture()) return 'active';
		if ($min->isFuture() && $max->isFuture()) return 'awaiting';

		return 'inactive';
	}

	protected function getSubscriptionDates() {
		$key = self::getSubscriptionKey($this->id);

		return \Cache::remember($key, 60 * 24, function() {
			if ($this->hasRole('admin') || $this->hasRole('moderator')) {
				return [Carbon::now()->subCentury(), Carbon::now()->addCentury()];
			}

			$dates = \DB::table('orders')
				->selectRaw('max(products.access_end) as max, min(products.access_start) as min')
				->join('products', 'orders.product_id', '=', 'products.id')
				->where('orders.user_id', $this->id)
				->where('orders.paid', 1)
				->first();

			return [Carbon::parse($dates->min), Carbon::parse($dates->max)];
		});
	}

	public static function getSubscriptionKey($id) {
		return sprintf(self::SUBSCRIPTION_DATES_CACHE_KEY, self::CACHE_VER, $id);
	}

	/**
	 * Send the password reset notification.
	 *
	 * @param  string $token
	 *
	 * @return void
	 */
	public function sendPasswordResetNotification($token)
	{
		$this->notify(new ResetPasswordNotification($token));
	}

	/**
	 * Get the current user or find by id.
	 *
	 * @param $id
	 * @param array $columns
	 *
	 * @return User|\Illuminate\Contracts\Auth\Authenticatable
	 */
	public static function fetch($id, $columns = ['*'])
	{
		if ($id === 'current') {
			return Auth::user();
		}

		return User::find($id, $columns);
	}

	/**
	 * Determine whether the user has the given role.
	 *
	 * @param $roleName
	 *
	 * @return bool
	 */
	public function hasRole($roleName)
	{
		foreach ($this->roles as $role) {
			if ($role->name === $roleName) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Determine whether the user is an admin.
	 *
	 * @return bool
	 */
	public function isAdmin()
	{
		return $this->hasRole('admin');
	}

	/**
	 * Query users of certain role.
	 *
	 * @param \Illuminate\Database\Eloquent\Builder $query
	 * @param string $role
	 *
	 * @return \Illuminate\Database\Query\Builder
	 */
	public function scopeOfRole($query, $role)
	{
		return $query
			->whereHas('roles', function ($query) use ($role) {
				return $query->where('name', $role);
			})->get();
	}
}
