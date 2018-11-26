<?php

namespace App\Models;

use App\Models\Concerns\Cached;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class QuizAnswer extends Model
{
	use Cached, SoftDeletes;

	protected $fillable = ['text', 'is_correct', 'quiz_question_id', 'hits'];

	protected $casts = [
		'is_correct' => 'boolean',
	];
}
