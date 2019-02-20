<?php

namespace App\Models;

use App\Scopes\OrderByOrderNumberScope;
use App\Traits\CourseProgressStats;
use Carbon\Carbon;
use Ramsey\Uuid\Uuid;
use Illuminate\Support\Facades\Auth;
use Illuminate\Notifications\Notifiable;
use App\Notifications\ResetPasswordNotification;
use Illuminate\Foundation\Auth\User as Authenticatable;
use ScoutEngines\Elasticsearch\Searchable;

class User extends Authenticatable
{
	use Notifiable, CourseProgressStats, Searchable;

	const SUBSCRIPTION_DATES_CACHE_KEY = '%s-%s-subscription-dates';
	const CACHE_VER = '2';
	const SUBSCRIPTION_STATUS_INACTIVE = 'inactive';
	const SUBSCRIPTION_STATUS_AWAITING = 'awaiting';
	const SUBSCRIPTION_STATUS_ACTIVE = 'active';

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

	protected $guarded = ['suspended', 'deleted_at'];

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

	public function personalData()
	{
		return $this->hasOne('App\Models\UserPersonalData');
	}

	public function billing()
	{
		return $this->hasOne('App\Models\UserBillingData');
	}

	public function settings()
	{
		return $this->hasOne('App\Models\UserSettings');
	}

	public function userAddress()
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

	public function lessonsAvailabilityUnordered()
	{
		return $this->belongsToMany('App\Models\Lesson', 'user_lesson')
			->withoutGlobalScope(OrderByOrderNumberScope::class);
	}

	public function reactables()
	{
		return $this->hasMany('App\Models\Reactable');
	}

	public function chatRooms()
	{
		return $this->belongsToMany('App\Models\ChatRoom');
	}

	public function subscription()
	{
		return $this->hasOne('App\Models\UserSubscription');
	}

	public function userTime() {
		return $this->hasMany('App\Models\UserTime');
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
		return $this->userAddress->street ?? '';
	}

	public function getPhoneAttribute($value)
	{
		return $this->userAddress->phone ?? '';
	}

	public function getRecipientAttribute()
	{
		return $this->userAddress->recipient ?? '';
	}

	public function getZipAttribute()
	{
		return $this->userAddress->zip ?? '';
	}

	public function getCityAttribute()
	{
		return $this->userAddress->city ?? '';
	}

	/**
	 * TODO: https://bethink.atlassian.net/browse/PLAT-556
	 * Returns users all identity numbers
	 * @return array An associate array with types of
	 */
	public function getIdentityNumbersAttribute() {
		$numbers = [
			[
				'type' => 'personal_identity_number',
				'value' => $this->personalData->personal_identity_number ?? null,
			],
			[
				'type' => 'identity_card_number',
				'value' => $this->personalData->identity_card_number ?? null,
			],
			[
				'type' => 'passport_number',
				'value' => $this->personalData->passport_number ?? null,
			],
		];

		return array_values(array_filter($numbers, function ($number) {
			return !empty($number['value']);
		}));
	}

	// TODO: https://bethink.atlassian.net/browse/PLAT-556
	public function getIdentityNumberAttribute()
	{
		return $this->identity_numbers[0]['value'] ?? null;
	}

	public function getIsSubscriberAttribute()
	{
		return !is_null(Subscriber::where('email', $this->email)->first());
	}

	public function getSubscriptionStatusAttribute()
	{
		$key = self::getSubscriptionKey($this->id);

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

	public function getFullAddressAttribute()
	{
		$addr = $this->userAddress;

		return "{$addr->street}, {$addr->zip} {$addr->city}";
	}

	protected function getSubscriptionStatus($dates)
	{
		if ($this->hasRole(['admin', 'moderator', 'test'])) return self::SUBSCRIPTION_STATUS_ACTIVE;

		list ($min, $max) = $dates;

		if (!$min || !$max) {
			return self::SUBSCRIPTION_STATUS_INACTIVE;
		}

		if ($min->isPast() && $max->isFuture()) return self::SUBSCRIPTION_STATUS_ACTIVE;
		if ($min->isFuture() && $max->isFuture()) return self::SUBSCRIPTION_STATUS_AWAITING;

		return self::SUBSCRIPTION_STATUS_INACTIVE;
	}

	protected function getSubscriptionDates()
	{
		$min = $this->subscription ? Carbon::parse($this->subscription->access_start) : null;
		$max = $this->subscription ? Carbon::parse($this->subscription->access_end) : null;

		return [$min, $max];
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
	 * @param array|string $roleNames
	 *
	 * @return bool
	 */
	public function hasRole($roleNames)
	{
		foreach ($this->roles as $role) {
			if (in_array($role->name, (array) $roleNames)) {
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
		return $this->hasRole(Role::ROLE_ADMIN);
	}

	/**
	 * Determine whether the user is a moderator.
	 *
	 * @return bool
	 */
	public function isModerator()
	{
		return $this->hasRole(Role::ROLE_MODERATOR);
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

	public function suspend()
	{
		$this->suspended = true;
		$this->save();
	}

	public function forget()
	{
		$now = Carbon::now();
		$this->profile()->update([
			'first_name' => 'account',
			'last_name' => 'deleted',
			'public_email' => null,
			'public_phone' => null,
			'username' => null,
			'avatar' => 'avatars/account-deleted.png',
			'city' => null,
			'university' => null,
			'specialization' => null,
			'help' => null,
			'interests' => null,
			'about' => null,
			'learning_location' => null,
			'display_name' => null,
			'deleted_at' => $now
		]);

		$this->update([
			'consent_newsletter' => null,
			'email' => 'KontoUsunięte'.Uuid::uuid4()->toString().'@bethink.pl'
		]);

		$this->deleted_at = $now;
		$this->save();

		if ($this->profile) {
			$this->profile->unsearchable();
		}
	}

	public static function getSubscriptionKey($id)
	{
		return sprintf(self::SUBSCRIPTION_DATES_CACHE_KEY, self::CACHE_VER, $id);
	}

	public function toSearchableArray() {
		return [
			'id' => $this->id,
			'email' => $this->email,
			'full_name' => $this->full_name,
			'profile' => $this->profile,
		];
	}
}
