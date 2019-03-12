<?php


namespace App\Http\Controllers\Api\Transformers;


use App\Models\Lesson;
use App\Http\Controllers\Api\ApiTransformer;

class LessonTransformer extends ApiTransformer
{
	protected $availableIncludes = ['screens', 'tags', 'qna_questions'];

	protected $parent;

	public function __construct($parentData = [])
	{
		$this->parent = $parentData;
	}

	public function transform(Lesson $lesson)
	{
		$data = [
			'id'                 => $lesson->id,
			'name'               => $lesson->name,
			'is_required'        => $lesson->is_required,
			'isAccessible'       => $lesson->isAccessible(),
			'isAvailable'        => $lesson->isAvailable(),
			'isDefaultStartDate' => $lesson->isDefaultStartDate(),
			'startDate'          => $lesson->getStartDate()->timestamp ?? null,
		];

		if ($this->parent) {
			$data = array_merge($data, $this->parent);
		}

		return $data;
	}

	public function includeScreens(Lesson $lesson)
	{
		$screens = $lesson->screens;

		$meta = [
			'lessonId' => $lesson->id,
		];
		$meta = array_merge($meta, $this->parent);

		return $this->collection($screens, new ScreenTransformer($meta), 'screens');
	}

	public function includeTags(Lesson $lesson)
	{
		$tags = $lesson->tags;

		return $this->collection($tags, new TagTransformer(['lessons' => $lesson->id]), 'tags');
	}

	public function includeQnaQuestions(Lesson $lesson)
	{
		$questions = $lesson->questions;

		return $this->collection($questions, new QnaQuestionTransformer($lesson->id), 'qna_questions');
	}
}
