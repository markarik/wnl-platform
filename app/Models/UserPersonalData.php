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
}
