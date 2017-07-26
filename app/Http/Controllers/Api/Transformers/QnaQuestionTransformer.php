<?php namespace App\Http\Controllers\Api\Transformers;


use App\Models\QnaQuestion;
use App\Http\Controllers\Api\ApiController;
use App\Http\Controllers\Api\ApiTransformer;


class QnaQuestionTransformer extends ApiTransformer
{
	protected $availableIncludes = ['qna_answers', 'tags', 'profiles'];
	protected $parent;

	public function __construct($parent = null)
	{
		$this->parent = $parent;
	}

	public function transform(QnaQuestion $question)
	{
		$data = [
			'id'         => $question->id,
			'text'       => $question->text,
			'created_at' => $question->created_at->timestamp,
			'updated_at' => $question->updated_at->timestamp,
		];

		if ($this->parent) {
			$data['lessons'] = $this->parent;
		}

		if (!empty($question->meta)) {
			$data['meta'] = $question->meta;
		}

		if (self::shouldInclude('reactions')) {
			$data = array_merge($data, ReactionsCountTransformer::transform($question));
		}

		return $data;
	}

	public function includeQnaAnswers(QnaQuestion $question)
	{
		$answers = $question->answers;

		return $this->collection($answers, new QnaAnswerTransformer, 'qna_answers');
	}

	public function includeTags(QnaQuestion $question)
	{
		$tags = $question->tags;

		return $this->collection($tags, new TagTransformer(['qna_questions' => $question->id]), 'tags');
	}

	public function includeProfiles(QnaQuestion $question)
	{
		$profile = $question->user->profile;

		return $this->item($profile, new UserProfileTransformer(['qna_questions' => $question->id]), 'profiles');
	}
}
