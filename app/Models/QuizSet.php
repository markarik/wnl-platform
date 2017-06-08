<?php

namespace App\Models;

use App\Models\Concerns\Cached;
use Illuminate\Database\Eloquent\Model;

class QuizSet extends Model
{
	use Cached;

	protected $fillable = ['name'];

	public function questions()
	{
		return $this->belongsToMany('App\Models\QuizQuestion');
	}
}
