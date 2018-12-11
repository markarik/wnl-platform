<?php


namespace App\Http\Controllers\Api\Transformers;


use App\Models\QnaAnswer;
use App\Http\Controllers\Api\ApiController;
use App\Http\Controllers\Api\ApiTransformer;


class QnaAnswerTransformer extends ApiTransformer
{
	protected $availableIncludes = ['profiles', 'comments'];

	public function transform(QnaAnswer $answer)
	{
		$data = [
			'id'            => $answer->id,
			'text'          => $answer->text,
			'qna_questions' => $answer->question->id ?? [],
			'created_at'    => $answer->created_at->timestamp,
			'updated_at'    => $answer->updated_at->timestamp,
		];

		if (self::shouldInclude('reactions')) {
			$data = array_merge($data, ReactionsCountTransformer::transform($answer));
		}

		return $data;
	}

	public function includeProfiles(QnaAnswer $answer)
	{
		$profile = $answer->user->profile;

		return $this->item($profile, new UserProfileTransformer(['qna_answers' => $answer->id]), 'profiles');
	}

	public function includeComments(QnaAnswer $answer)
	{
		$comments = $answer->comments;

		return $this->collection($comments, new CommentTransformer(['qna_answers' => $answer->id]), 'comments');
	}
}
