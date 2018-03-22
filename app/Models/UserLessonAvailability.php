<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserLessonAvailability extends Model
{
	protected $fillable = ['user_id', 'lesson_id', 'start_date'];
	protected $dates = ['start_date'];
}
