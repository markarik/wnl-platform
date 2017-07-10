<?php

namespace App\Models;

use Carbon\Carbon;
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
		'seen_at',
	];

	public function setReadAtAttribute($value)
	{
		$this->attributes['read_at'] = $value === 'now' ? Carbon::now() : $value;
	}

	public function setSeenAtAttribute($value)
	{
		$this->attributes['seen_at'] = $value === 'now' ? Carbon::now() : $value;
	}
}
