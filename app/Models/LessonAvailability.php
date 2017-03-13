<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LessonAvailability extends Model
{
	protected $dates = ['start_date'];

    protected $fillable = ['lesson_id', 'edition_id', 'start_date'];
}
