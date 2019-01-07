<?php

namespace App\Http\Controllers\Api\Transformers;

use App\Http\Controllers\Api\ApiTransformer;
use App\Models\Discussion;

class DiscussionTransformer extends ApiTransformer
{
	protected $availableIncludes = ['qna_questions'];
	protected $parent;

	public function __construct($parent = [])
	{
		$this->parent = $parent;
	}

	public function transform(Discussion $discussion)
	{
		$data = [
			'id' => $discussion->id,
			'name' => $discussion->name,
		];

		return $data;
	}

	public function includeQnaQuestions(Discussion $discussion)
	{
		$qnaQuestions = $discussion->questions;

		return $this->collection($qnaQuestions, new QnaQuestionTransformer(['discussions' => $discussion->id]), 'qna_questions');
	}
}
