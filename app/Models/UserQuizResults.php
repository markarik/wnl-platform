<?php

namespace App\Models;

use App\Models\Concerns\Cached;
use Illuminate\Database\Eloquent\Model;

class UserQuizResults extends Model
{
	use Cached;
	public $timestamps = false;

	protected $fillable = ['user_id', 'quiz_answer_id', 'quiz_question_id'];

	public function user()
	{
		return $this->belongsTo('\App\Models\User');
	}

	public function quizAnswer()
	{
		return $this->belongsTo('\App\Models\QuizAnswer');
	}

	public function quizQuestion()
	{
		return $this->belongsTo('\App\Models\QuizQuestion');
	}
}
