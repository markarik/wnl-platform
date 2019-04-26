<?php


namespace App\Http\Controllers\Api\Transformers;


use App\Http\Controllers\Api\ApiTransformer;
use App\Models\QnaAnswer;


class QnaAnswerTransformer extends ApiTransformer
{
	protected $availableIncludes = ['profiles', 'comments'];
	protected $parent;

	public function __construct($parent = null)
	{
		$this->parent = $parent;
	}

	public function transform(QnaAnswer $answer)
	{
		$data = [
			'id'            => $answer->id,
			'text'          => $answer->text,
			'qna_questions' => $answer->question_id,
			'created_at'    => $answer->created_at->timestamp,
			'updated_at'    => $answer->updated_at->timestamp,
		];

		if ($this->parent) {
			$data = array_merge($data, $this->parent);
		}

		if (self::shouldInclude('reactions')) {
			$data = array_merge($data, ReactionsCountTransformer::transform($answer));
		}

		return $data;
	}

	public function includeProfiles(QnaAnswer $answer)
	{
		$profile = $answer->profiles;

		return $this->item($profile, new ProfileTransformer(['qna_answers' => $answer->id]), 'profiles');
	}

	public function includeComments(QnaAnswer $answer)
	{
		$comments = $answer->comments;

		return $this->collection($comments, new CommentTransformer(['qna_answers' => $answer->id]), 'comments');
	}
}
