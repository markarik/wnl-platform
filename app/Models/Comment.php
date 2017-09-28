<?php

namespace App\Models;

use App\Events\CommentPosted;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
	protected $fillable = ['text', 'user_id'];

	protected $dispatchesEvents = [
		'created' => CommentPosted::class,
	];

	public function user()
	{
		return $this->belongsTo('App\Models\User');
	}

	public function reactions()
	{
		return $this->morphToMany('App\Models\Reaction', 'reactable');
	}

	/**
	 * Get all of the owning commentable models.
	 */
	public function commentable()
	{
		return $this->morphTo();
	}
}
