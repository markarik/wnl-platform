<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
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

	protected $casts = [
		'labels'  => 'array',
		'context' => 'array',
		'id'      => 'uuid',
	];

	public $incrementing = false;


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
