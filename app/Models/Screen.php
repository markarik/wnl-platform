<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Screen extends Model
{
	protected $casts = [
		'meta' => 'json',
	];

	protected $fillable = ['content', 'type', 'name'];

	public function lesson()
	{
		return $this->belongsTo('\App\Models\Lesson');
	}

	public function sections()
	{
		return $this->hasMany('\App\Models\Section');
	}
}
