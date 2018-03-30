<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LessonUserAccess extends Model
{
	protected $table = 'lesson_user_access';

	protected $casts = ['access' => 'boolean'];
}
