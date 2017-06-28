<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
	protected $fillable = ['id', 'data', 'notifiable_id', 'notifiable_type', 'read_at', 'type'];

	protected $casts = [
		'data' => 'array',
		'id'   => 'string',
	];

	protected $dates = [
		'read_at',
	];
}
