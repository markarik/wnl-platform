<?php


namespace App\Http\Controllers\Api\Transformers;


use App\Models\QnaAnswer;
use App\Models\Lesson;
use App\Models\QnaQuestion;
use League\Fractal\TransformerAbstract;


class QnaAnswerTransformer extends TransformerAbstract
{
	protected $availableIncludes = ['profile', 'comments'];

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

	public function includeProfile(QnaAnswer $answer)
	{
		$profile = $answer->user->profile;

		return $this->item($profile, new UserProfileTransformer(['answers' => $answer->id]), 'answers');
	}

	public function includeComments(QnaAnswer $answer)
	{
		$comments = $answer->comments;

		return $this->collection($comments, new CommentTransformer(['answers' => $answer->id]), 'answers');
	}
}
