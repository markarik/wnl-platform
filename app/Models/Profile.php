<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Lib\Bethink\Bethink;
use ScoutEngines\Elasticsearch\Searchable;

class Profile extends Model
{
	use Searchable;

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

	protected $guarded = ['deleted_at'];

	protected $table = 'user_profiles';

	public function user()
	{
		return $this->belongsTo('App\Models\User');
	}

	public function roles() {
		return $this->belongsToMany('App\Models\Role', 'role_user', 'user_id', 'role_id', 'user_id');
	}

	public function getAvatarUrlAttribute()
	{
		return (new Bethink)->getAssetPublicUrl($this->avatar) ?? null;
	}

	public function getFirstNameAttribute()
	{
		if (!is_null($this->deleted_at)) {
			return trans('profile.account-deleted-first-name');
		}

		return $this->original['first_name'];
	}

	public function getLastNameAttribute()
	{
		if (!is_null($this->deleted_at)) {
			return trans('profile.account-deleted-last-name');
		}

		return $this->original['last_name'];
	}

	public function getFullNameAttribute()
	{
		if (!is_null($this->deleted_at)) {
			return trans('profile.account-deleted-full-name');
		}

		return $this->first_name . ' ' . $this->last_name;
	}

	public function setUsernameAttribute($value)
	{
		$this->attributes['username'] = $value === '' ? null : $value;
	}

	public function getRolesNamesAttribute()
	{
		return $this->roles->pluck('name')->toArray();
	}
}
