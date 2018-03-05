<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChatMessage extends Model
{
	protected $fillable = ['content', 'user_id', 'room_id', 'time'];

	public $timestamps = false;

	public function user()
	{
		return $this->belongsTo('App\Models\User');
	}

	public function chatRoom()
	{
		return $this->belongsTo('App\Models\ChatRoom');
	}

	public function getContentAttribute($value) {
		return decrpty($value);
	}

	public function setContentAttribute($value) {
		$this->attributes['content'] = encrypt($value);
	}
}
