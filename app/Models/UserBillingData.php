<?php

namespace App\Models;

use App\Events\Users\UserDataUpdated;
use Illuminate\Database\Eloquent\Model;

class UserBillingData extends Model
{
	protected $table = 'user_billing_data';

	protected $dispatchesEvents = [
		'updated' => UserDataUpdated::class,
	];

	protected $fillable = [
		'company_name',
		'vat_id',
		'address',
		'zip',
		'city',
		'country',
	];
}
