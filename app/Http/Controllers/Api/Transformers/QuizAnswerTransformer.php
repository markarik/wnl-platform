<?php


namespace App\Http\Controllers\Api\Transformers;


use App\Models\QuizAnswer;
use App\Http\Controllers\Api\ApiTransformer;

class QuizAnswerTransformer extends ApiTransformer
{
	protected $parent;

	public function __construct($parent = null)
	{
		$this->parent = $parent;
	}


	public function transform(QuizAnswer $quizAnswer)
	{
		$data = [
			'id'         => $quizAnswer->id,
			'text'       => $quizAnswer->text,
			'is_correct' => $quizAnswer->is_correct,
			'hits'       => $quizAnswer->hits,
		];

		if ($this->parent) {
			$data = array_merge($data, $this->parent);
		}

		return $data;
	}
}
