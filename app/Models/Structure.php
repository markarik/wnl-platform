<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Structure extends Model
{
    protected $fillable = ['name', 'subject_id'];

	public function subject() {
		return $this->belongsTo('\App\Models\Subject');
	}

	public function screens() {
		return $this->hasMany('\App\Models\Screen');
	}

	public function snippets() {
		return $this->hasManyThrough('\App\Models\Snippet', '\App\Models\Screen');
	}

	public function sections() {
		return $this->hasMany('\App\Models\Section');
	}
}
