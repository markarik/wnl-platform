<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserTime extends Model
{
	public $timestamps = false;

	protected $table = 'user_time';
	protected $fillable = ['user_id', 'time', 'created_at', 'updated_at'];

	public function user()
	{
		return belongsTo('App\Models\User');
	}
}
