<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserProfile extends Model
{
	protected $fillable = [
		'first_name',
		'last_name',
		'public_email',
		'public_phone',
		'username',
		'personal_settings',
	];

	protected $casts = [
		'personal_settings' => 'array',
	];

	public function getAvatarUrlAttribute()
	{
		return $this->avatar ? asset('storage/' . $this->avatar) : null;
	}

	public function getFullNameAttribute()
	{
		return "$this->first_name $this->last_name";
	}

	public function getSettingsAttribute()
	{
		return array_merge((array)$this->personal_settings, config('user-default-settings'));
	}
}
