<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserBilling extends Model
{
	protected $fillable = [
		'name',
		'vat_id',
		'address',
		'zip',
		'city',
		'country',
	];
}
