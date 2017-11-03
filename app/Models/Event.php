<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
	protected $fillable = ['id', 'data', 'task_id'];

	protected $casts = [
		'data'    => 'array',
		'id'      => 'string',
		'task_id' => 'string',
	];
}
