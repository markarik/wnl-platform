<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserSettings extends Model
{

	protected $fillable = [
		'consent_newsletter',
		'consent_account',
		'consent_order',
		'consent_terms',
		'notifications_email',
		'notifications_sms',
	];

	protected $casts = [
		'consent_newsletter'  => 'bool',
		'consent_account'     => 'bool',
		'consent_order'       => 'bool',
		'consent_terms'       => 'bool',
		'notifications_email' => 'bool',
		'notifications_sms'   => 'bool',
	];

}
