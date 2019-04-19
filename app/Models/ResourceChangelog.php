<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ResourceChangelog extends Model
{
	public $table = 'resource_changelog';

	protected $fillable = ['resource_id', 'resource_type', 'property', 'value', 'user_id'];

	public function user() {
		return $this->belongsTo('App\\Models\\User');
	}

	public function resource() {
		return $this->morphTo();
	}
}
