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
	];

	public function user()
	{
		return $this->belongsTo('App\Models\User');
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
