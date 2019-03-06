<?php

namespace App\Models;

use App\Models\Concerns\Cached;
use Illuminate\Database\Eloquent\Model;

class QuizSet extends Model
{
	use Cached;

	protected $fillable = ['name', 'description', 'lesson_id'];

	public function questions()
	{
		return $this->belongsToMany(
			'\App\Models\QuizQuestion',
			'quiz_question_quiz_set',
			'quiz_set_id',
			'quiz_question_id'
		)
			->withPivot('order_number')
			->orderBy('pivot_order_number');
	}

	public function syncQuestions($questionsIds)
	{
		$orderNumber = 0;
		$questionsSync = [];
		foreach($questionsIds as $questionId) {
			$questionsSync[$questionId] = ['order_number' => $orderNumber++];
		}

		$this->questions()->sync($questionsSync);
	}
}
