<?php


namespace App\Http\Controllers\Api\Transformers;


use App\Models\Answer;
use App\Models\Lesson;
use App\Models\Question;
use League\Fractal\TransformerAbstract;


class AnswerTransformer extends TransformerAbstract
{
	protected $availableIncludes = ['users'];

	public function transform(Answer $answer)
	{
		$data = [
			'id'        => $answer->id,
			'text'      => $answer->text,
			'questions' => $answer->question->id,
			'time'      => $answer->created_at->formatLocalized('%A %d %B %Y'),
		];

		return $data;
	}

	public function includeUsers(Answer $answer)
	{
		$user = $answer->user;

		return $this->item($user, new UserTransformer(['answers' => $answer->id]), 'users');
	}
}
