<?php

namespace App\Models;

use App\Models\Concerns\Cached;
use Illuminate\Database\Eloquent\Model;

class UserCourseProgress extends Model
{
	use Cached;
	public $timestamps = false;
	protected $primaryKey = 'unique_user_course_progress';

	protected $fillable = ['user_id', 'lesson_id', 'status'];

	public function user()
	{
		return $this->belongsTo('\App\Models\User');
	}

	public function lesson()
	{
		return $this->belongsTo('\App\Models\Lesson');
	}
}
