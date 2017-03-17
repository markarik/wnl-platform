<?php


namespace App\Http\Controllers\Api\Transformers;


use App\Models\Lesson;
use App\Models\Question;
use League\Fractal\TransformerAbstract;


class QuestionTransformer extends TransformerAbstract
{
	protected $availableIncludes = ['answers', 'tags'];
	protected $parent;

	public function __construct($parent = null)
	{
		$this->parent = $parent;
	}

	public function transform(Question $question)
	{
		$data = [
			'id'   => $question->id,
			'text' => $question->text,
		];

		if ($this->parent) {
			$data['lessons'] = $this->parent;
		}

		return $data;
	}

	public function includeAnswers(Question $question)
	{
		$answers = $question->answers;

		return $this->collection($answers, new AnswerTransformer, 'answers');
	}

	public function includeTags(Question $question)
	{
		$tags = $question->tags;

		return $this->collection($tags, new TagTransformer, 'tags');
	}
}
