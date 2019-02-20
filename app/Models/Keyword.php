<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Keyword extends Model
{
	protected $fillable = ['text'];

	public function annotation()
	{
		return $this->belongsTo('App\Models\Annotation');
	}
}
