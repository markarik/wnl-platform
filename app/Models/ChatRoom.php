<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChatRoom extends Model
{
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

	public function getIsPrivateAttribute()
	{
		return $this->type === 'private';
	}

	public function getIsPublicAttribute()
	{
		return !$this->type === 'public';
	}

	public function scopeOfName($query, $name)
	{
		return $query
			->where('name', $name);
	}
}
