<?php

namespace App\Models;

use App\Models\Concerns\Cached;
use Illuminate\Database\Eloquent\Model;

class UserCourseProgress extends Model
{
	public $timestamps = false;
	protected $table = 'user_course_progress';

	protected $fillable = ['user_id', 'lesson_id', 'status'];
}
