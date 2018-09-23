<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserPersonalData extends Model
{
	protected $table = 'user_personal_data';

	protected $fillable = [
		'user_id',
		'personal_identity_number',
		'identity_card_number',
		'passport_number'
	];

	public function user()
	{
		return $this->belongsTo('App\Models\User');
	}

	public function getPersonalIdentityNumberAttribute($value)
	{
		return is_null($value) ? null : decrypt($value);
	}

	public function setPersonalIdentityNumberAttribute($value)
	{
		$this->attributes['personal_identity_number'] = encrypt($value);
	}

	public function getIdentityCardNumberAttribute($value)
	{
		return is_null($value) ? null : decrypt($value);
	}

	public function setIdentityCardNumberAttribute($value)
	{
		$this->attributes['identity_card_number'] = encrypt($value);
	}

	public function getPassportNumberAttribute($value)
	{
		return is_null($value) ? null : decrypt($value);
	}

	public function setPassportNumberAttribute($value)
	{
		$this->attributes['passport_number'] = encrypt($value);
	}
}
