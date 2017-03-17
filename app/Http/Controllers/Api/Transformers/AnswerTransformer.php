<?php


namespace App\Http\Controllers\Api\Transformers;


use App\Models\Answer;
use App\Models\Lesson;
use App\Models\Question;
use League\Fractal\TransformerAbstract;


class AnswerTransformer extends TransformerAbstract
{
	public function transform(Answer $answer)
	{
		$data = [
			'id'        => $answer->id,
			'text'      => $answer->text,
			'questions' => $answer->question->id,
		];

		return $data;
	}
}
