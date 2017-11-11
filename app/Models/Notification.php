<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;

class Notification extends Model
{
	use Searchable;

	protected $fillable = ['id', 'data', 'channel', 'notifiable_id', 'notifiable_type', 'read_at',
		'seen_at', 'type', 'event_id'];

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
