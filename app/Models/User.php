<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
	use Notifiable;

	protected $casts = [
		'invoice'            => 'boolean',
		'consent_newsletter' => 'boolean',
		'consent_account'    => 'boolean',
		'consent_order'      => 'boolean',
		'consent_terms'      => 'boolean',
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

	public function orders()
	{
		return $this->hasMany('App\Models\Order');
	}

	public function getFullNameAttribute()
	{
		return $this->first_name . ' ' . $this->last_name;
	}

	public function getFirstNameAttribute($value)
	{
		return decrypt($value);
	}

	public function setFirstNameAttribute($value)
	{
		$this->attributes['first_name'] = encrypt($value);
	}

	public function getLastNameAttribute($value)
	{
		return decrypt($value);
	}

	public function setLastNameAttribute($value)
	{
		$this->attributes['last_name'] = encrypt($value);
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
}
