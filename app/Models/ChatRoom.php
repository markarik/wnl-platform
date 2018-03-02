<?php

namespace App\Models;

use App\Models\Concerns\Restrictable;
use Illuminate\Database\Eloquent\Model;

class ChatRoom extends Model
{
	use Restrictable;

	protected $fillable = ['name', 'type'];

	protected $appends = ['is_private', 'is_public'];

	public function messages()
	{
		return $this->hasMany('App\Models\ChatMessage');
	}

	public function users()
	{
		return $this->belongsToMany('App\Models\User');
	}

	public function lessons()
	{
		return $this->morphedByMany('App\Models\Lesson', 'chat_roomable', 'chat_roomables');
	}

	public function roles()
	{
		return $this->morphedByMany('App\Models\Role', 'chat_roomable', 'chat_roomables');
	}

	public function permissions()
	{
		return $this->morphToMany('App\Models\Permission', 'restrictable');
	}

	public function getIsPrivateAttribute()
	{
		return $this->type === 'private';
	}

	public function getIsPublicAttribute()
	{
		return $this->type === 'public';
	}

	public function scopeOfName($query, $name)
	{
		return $query
			->where('name', $name);
	}
}
