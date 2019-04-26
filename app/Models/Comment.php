<?php

namespace App\Models;

use App\Events\Comments\CommentPosted;
use App\Models\Contracts\WithReactions;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Comment extends Model implements WithReactions
{
	use SoftDeletes;

	protected $fillable = ['text', 'user_id'];
	protected $dates = ['deleted_at'];

	protected $dispatchesEvents = [
		'created' => CommentPosted::class,
	];

	public function user()
	{
		return $this->belongsTo('App\Models\User');
	}

	public function profiles()
	{
		return $this->belongsTo('App\Models\Profile', 'user_id', 'user_id');
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
