<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lesson extends Model
{
    protected $fillable = ['name'];

	public function subject() {
		return $this->belongsTo('\App\Models\Subject');
	}

	public function screens() {
		return $this->hasMany('\App\Models\Screen');
	}
}
