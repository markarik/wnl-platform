<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
	protected $fillable = [
		'id',
		'creator_id',
		'assignee_id',
		'priority',
		'order',
		'status',
		'text',
		'labels',
		'context',
	];

	protected $casts = [
		'labels'  => 'array',
		'context' => 'array',
	];

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
