<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Discussion extends Model
{
	protected $fillable = ['name'];

	public function questions() {
		return $this->hasMany('\App\Models\QnaQuestion', 'discussion_id');
	}

	public function screens() {
		return $this->morphedByMany('App\Models\Screen', 'discussable');
	}

	public function pages() {
		return $this->morphedByMany('App\Models\Page', 'discussable');
	}
}
