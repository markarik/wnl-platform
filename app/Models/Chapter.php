<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Chapter extends Model
{
    protected $fillable = ['name'];

	public function module() {
		return $this->belongsTo('\App\Models\Module');
	}

	public function sections() {
		return $this->hasMany('\App\Models\Section');
	}
}
