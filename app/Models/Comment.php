<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
	protected $fillable = ['text', 'user_id'];

	public function user()
	{
		return $this->belongsTo('App\Models\User');
	}

	/**
	 * Get all of the owning commentable models.
	 */
	public function commentable()
	{
		return $this->morphTo();
	}
}
