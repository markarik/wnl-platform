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
		return $this->belongsToMany('\App\Models\Screen', 'screens');
	}

	public function sections() {
		return $this->hasMany('\App\Models\Section');
	}

	public function course()
	{
		return $this->hasOne('\App\Models\Course');
	}
}
