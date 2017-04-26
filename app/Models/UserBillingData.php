<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserBillingData extends Model
{
	protected $table = 'user_billing_data';

	protected $fillable = [
		'company_name',
		'vat_id',
		'address',
		'zip',
		'city',
		'country',
	];
}
