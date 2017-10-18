<?php

namespace App\Models;

use App\Models\Concerns\Cached;
use Illuminate\Database\Eloquent\Model;

class UserCourseProgress extends Model
{
	protected $table = 'user_course_progress';

	protected $fillable = ['user_id', 'lesson_id', 'screen_id', 'section_id', 'status'];
}
