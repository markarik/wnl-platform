<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Lib\Bethink\Bethink;
use ScoutEngines\Elasticsearch\Searchable;

class UserProfile extends Model
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
		return (new Bethink)->getAssetPublicUrl($this->avatar) ?? null;
	}

	public function getFullNameAttribute()
	{
		$fullName = $this->first_name . ' ' . $this->last_name;

		if ($fullName === 'account deleted') {
			$fullName = 'Konto usunięte';
		}

		return $fullName;
	}

	public function setUsernameAttribute($value)
	{
		$this->attributes['username'] = $value === '' ? null : $value;
	}
}
