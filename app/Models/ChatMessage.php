<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChatMessage extends Model
{
	protected $fillable = ['content', 'user_id', 'room_id'];

	protected function user()
	{
		return $this->belongsTo('App\Models\User');
	}
}
