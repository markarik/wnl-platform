<?php

namespace App\Models;

use App\Models\Concerns\Cached;
use Illuminate\Database\Eloquent\Model;
use ScoutEngines\Elasticsearch\Searchable;

class QuizSet extends Model
{
	use Cached, Searchable;

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
			->orderBy('order_number');
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
