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
}
