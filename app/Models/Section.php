<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Section extends Model
{
	protected $fillable = ['name', 'screen_id'];

	public function slides() {
		return $this->morphToMany('\App\Models\Slide', 'presentable');
	}

	public function screen()
	{
		return $this->belongsTo('App\Models\Screen');
	}
}
