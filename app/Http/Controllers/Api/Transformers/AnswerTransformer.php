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
			'id'         => $answer->id,
			'text'       => $answer->text,
			'questions'  => $answer->question->id,
			'created_at' => $answer->created_at->timestamp,
			'updated_at' => $answer->updated_at->timestamp,
		];

		return $data;
	}

	public function includeUsers(QnaAnswer $answer)
	{
		$user = $answer->user;

		return $this->item($user, new UserProfileTransformer(['answers' => $answer->id]), 'users');
	}
}
