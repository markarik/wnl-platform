<?php

namespace App\Models;

use App\Events\Users\UserDataUpdated;
use Facades\Lib\Bethink\Bethink;
use Illuminate\Database\Eloquent\Model;
use ScoutEngines\Elasticsearch\Searchable;

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
		'display_name',
		'username',
		'city',
		'university',
		'specialization',
		'help',
		'interests',
		'about',
		'learning_location',
	];

	protected $guarded = ['deleted_at'];

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
		return Bethink::getAssetPublicUrl($this->avatar) ?? null;
	}

	public function getFullNameAttribute()
	{
		return "$this->first_name $this->last_name";
	}

	public function setUsernameAttribute($value)
	{
		$this->attributes['username'] = $value === '' ? null : $value;
	}

	public function getDisplayNameAttribute()
	{
		if ($this->attributes['display_name']) {
			return $this->attributes['display_name'];
		} else {
			return $this->full_name;
		}

	}
}
