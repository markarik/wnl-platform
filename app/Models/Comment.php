<?php

namespace App\Models;

use Altek\Accountant\Contracts\Recordable;
use App\Events\Comments\CommentPosted;
use App\Models\Contracts\WithReactions;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use \Altek\Accountant\Recordable as RecordableTrait;

class Comment extends Model implements WithReactions, Recordable
{
	use SoftDeletes, RecordableTrait;

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
		return $this->belongsTo('App\Models\UserProfile', 'user_id', 'user_id');
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
