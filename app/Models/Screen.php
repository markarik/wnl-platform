<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Screen extends Model
{
	protected $fillable = ['snippet_id'];

    public function snippet() {
    	return $this->belongsTo('\App\Models\Snippet');
	}

	public function lesson() {
		return $this->belongsTo('\App\Models\Structure');
	}

	public function slides() {
		return $this->hasManyThrough('\App\Models\Slide', '\App\Models\Snippet');
	}
}
