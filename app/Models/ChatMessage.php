<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChatMessage extends Model
{
	const MAX_MSG_CONTENT_LEN = 65000;

	protected $fillable = ['content', 'user_id', 'room_id', 'time'];

	public $timestamps = false;

	public function user()
	{
		return $this->belongsTo('App\Models\User');
	}

	public function profiles()
	{
		return $this->belongsTo('App\Models\Profile', 'user_id', 'user_id');
	}

	public function chatRoom()
	{
		return $this->belongsTo('App\Models\ChatRoom');
	}

	public function getContentAttribute($value) {
		return decrypt($value);
	}

	public function setContentAttribute($value) {
		$encrypted = encrypt($value);
		if (strlen($encrypted) < self::MAX_MSG_CONTENT_LEN) {
			$this->attributes['content'] = encrypt($value);
		} else {
			\Log::error("Message with ID {$this->id} is too long for encryption. Skipping");
		}
	}
}
