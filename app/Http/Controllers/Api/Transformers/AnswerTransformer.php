<?php


namespace App\Http\Controllers\Api\Transformers;


use App\Models\QnaAnswer;
use App\Models\Lesson;
use App\Models\QnaQuestion;
use League\Fractal\TransformerAbstract;


class AnswerTransformer extends TransformerAbstract
{
	protected $availableIncludes = ['users'];

	public function transform(QnaAnswer $answer)
	{
		$data = [
			'id'        => $answer->id,
			'text'      => $answer->text,
			'questions' => $answer->question->id,
			'time'      => $answer->created_at->formatLocalized('%A %d %B %Y'),
		];

		return $data;
	}

	public function includeUsers(QnaAnswer $answer)
	{
		$user = $answer->user;

		return $this->item($user, new UserTransformer(['answers' => $answer->id]), 'users');
	}
}
