<?php namespace App\Http\Controllers\Api\Transformers;


use App\Http\Controllers\Api\ApiTransformer;
use App\Models\QnaQuestion;


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
			'discussions' => $question->discussion_id
		];

		if ($this->parent) {
			$data = array_merge($data, $this->parent);
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
		$answers = $question->qnaAnswers;

		return $this->collection($answers, new QnaAnswerTransformer(['qna_questions' => $question->id]), 'qna_answers');
	}

	public function includeTags(QnaQuestion $question)
	{
		$tags = $question->tags;

		return $this->collection($tags, new TagTransformer(['qna_questions' => $question->id]), 'tags');
	}

	public function includeProfiles(QnaQuestion $question)
	{
		$profile = $question->profiles;

		return $this->item($profile, new ProfileTransformer(['qna_questions' => $question->id]), 'profiles');
	}
}
