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

	// https://laracasts.com/discuss/channels/laravel/timestamps-error-when-usingpivotmodel
	public function getCreatedAtColumn() {
		return 'created_at';
	}

	public function getUpdatedAtColumn() {
		return 'updated_at';
	}
}
