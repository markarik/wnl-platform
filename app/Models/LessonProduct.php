<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LessonProduct extends Model
{
	protected $fillable = ['lesson_id', 'product_id', 'start_date'];
	protected $dates = ['start_date', 'created_at', 'updated_at'];
	protected $table = 'lesson_product';

	public function lesson() {
		return $this->belongsTo('\App\Models\Lesson');
	}
}
