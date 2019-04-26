<?php

namespace App\Models;

use App\Models\Contracts\WithReactions;
use App\Models\Contracts\WithTags;
use Illuminate\Database\Eloquent\Model;

class Task extends Model implements WithReactions, WithTags
{
	protected $fillable = [
		'id',
		'notifiable_id',
		'notifiable_type',
		'subject_type',
		'subject_id',
		'team',
		'creator_id',
		'assignee_id',
		'priority',
		'order',
		'status',
		'text',
		'labels',
	];

	const STATUS_DONE = 'done';
	const STATUS_REOPEN = 'reopen';

	protected $casts = [
		'labels'  => 'array',
		'context' => 'array',
		'id'      => 'uuid',
	];

	public $incrementing = false;

	public function events()
	{
		return $this->hasMany('App\Models\Event');
	}

	public function assignee()
	{
		return $this->belongsTo('App\Models\User', 'assignee_id', 'id');
	}

	public function assigneeProfiles()
	{
		return $this->belongsTo('App\Models\Profile', 'assignee_id', 'user_id');
	}

	public function comments()
	{
		return $this->morphMany('App\Models\Comment', 'commentable');
	}

	public function reactions()
	{
		return $this->morphToMany('App\Models\Reaction', 'reactable');
	}

	public function tags()
	{
		return $this->morphToMany('App\Models\Tag', 'taggable');
	}
}
