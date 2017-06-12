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
	];

	public function user()
	{
		return $this->belongsTo('App\Models\User');
	}

	public function getAvatarUrlAttribute()
	{
		return $this->avatar ? asset('storage/' . $this->avatar) : null;
	}

	public function getFullNameAttribute()
	{
		return "$this->first_name $this->last_name";
	}
}
