<?php

namespace App\Models;

use App\Events\UserDataUpdated;
use Laravel\Scout\Searchable;
use Facades\Lib\Bethink\Bethink;
use Illuminate\Database\Eloquent\Model;

class UserProfile extends Model
{
	use Searchable;

	protected $dispatchesEvents = [
		'updated' => UserDataUpdated::class,
	];

	protected $fillable = [
		'first_name',
		'last_name',
		'public_email',
		'public_phone',
		'username',
		'city',
		'university',
		'specialization',
		'help',
		'interests',
		'about',
		'learning_location',
	];

	public function user()
	{
		return $this->belongsTo('App\Models\User');
	}

	public function roles() {
		// hacky way of fixing problem with belongsToMany ignoring the third argument
		// github issue https://github.com/laravel/framework/issues/17240
		$this->primaryKey = 'user_id';

		return $this->belongsToMany('App\Models\Role', 'role_user', 'user_id', 'role_id');
	}

	public function getAvatarUrlAttribute()
	{
		return $this->avatar ? Bethink::appUrlAsset("storage/{$this->avatar}") : null;
	}

	public function getFullNameAttribute()
	{
		return "$this->first_name $this->last_name";
	}

	public function setUsernameAttribute($value)
	{
		$this->attributes['username'] = $value === '' ? null : $value;
	}
}
