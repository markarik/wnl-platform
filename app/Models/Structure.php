<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Structure extends Model
{
    protected $fillable = ['name', 'subject_id'];

	public function subject() {
		return $this->belongsTo('\App\Models\Subject');
	}

	public function snippets() {
		return $this->belongsToMany('\App\Models\Snippet', 'screens');
	}

	public function sections() {
		return $this->hasMany('\App\Models\Section');
	}
}
