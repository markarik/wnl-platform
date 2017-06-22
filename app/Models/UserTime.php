<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserTime extends Model
{
	public $timestamps = false;
	protected $primaryKey = 'user_id';
	protected $table = 'user_time';

	protected $fillable = ['user_id', 'time'];
}
