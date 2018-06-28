<?php

namespace App\Models;

use App\Events\Users\UserDataUpdated;
use Illuminate\Database\Eloquent\Model;

class UserAddress extends Model
{
	protected $dispatchesEvents = [
		'updated' => UserDataUpdated::class,
	];

	protected $fillable = [
		'street',
		'zip',
		'city',
		'phone',
		'recipient',
	];
}
