<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class UserLesson extends Pivot
{
	protected $fillable = ['lesson_id', 'user_id', 'start_date'];
	protected $dates = ['start_date'];
	protected $table = 'user_lesson';

	// https://laracasts.com/discuss/channels/laravel/timestamps-error-when-usingpivotmodel
	public function getCreatedAtColumn() {
		return 'created_at';
	}

	public function getUpdatedAtColumn() {
		return 'updated_at';
	}
}
