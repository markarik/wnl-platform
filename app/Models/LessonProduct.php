<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class LessonProduct extends Pivot
{
	protected $fillable = ['lesson_id', 'product_id', 'start_date'];
	protected $dates = ['start_date'];

	public function lesson() {
		return $this->belongsTo('\App\Models\Lesson');
	}
}
