<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
	protected $fillable = ['slug', 'name', 'description'];

	public function chatRooms()
	{
		return $this->morphedByMany('App\Models\ChatRoom', 'restrictable');
	}

	public function scopeSlug($query, $slug)
	{
		return $query
			->where('slug', $slug)
			->first();
	}
}
