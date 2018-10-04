<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SiteWideMessage extends Model
{
	protected $fillable = ['message', 'read_at', 'start_date', 'end_date', 'user_id', 'slug'];

	protected $casts = [
		'start_date' => 'date',
		'end_date'   => 'date'
	];
}
