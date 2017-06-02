<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChatRoom extends Model
{
	protected $fillable = ['name'];

	protected $appends = ['is_private', 'is_public'];

	public function messages()
	{
		return $this->hasMany('App\Models\ChatMessage');
	}

	public function getIsPrivateAttribute()
	{
		return str_is('private-*', $this->name);
	}

	public function getIsPublicAttribute()
	{
		return !$this->is_private;
	}

	public function scopeOfName($query, $name)
	{
		return $query
			->where('name', $name);
	}
}
