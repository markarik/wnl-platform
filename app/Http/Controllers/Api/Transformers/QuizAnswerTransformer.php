<?php


namespace App\Http\Controllers\Api\Transformers;


use App\Models\QuizAnswer;
use League\Fractal\TransformerAbstract;

class QuizAnswerTransformer extends TransformerAbstract
{
	protected $parent;

	public function __construct($parent = null)
	{
		$this->parent = $parent;
	}


	public function transform(QuizAnswer $quizAnswer)
	{
		$data = [
			'id'             => $quizAnswer->id,
			'text'           => $quizAnswer->text,
			'is_correct'     => $quizAnswer->is_correct,
			'preserve_order' => $quizAnswer->preserve_order,
		];

		if ($this->parent) {
			$data = array_merge($data, $this->parent);
		}

		return $data;
	}
}
