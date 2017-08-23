<?php


namespace App\Http\Controllers\Api\Transformers;


use App\Models\UserQuizResults;
use App\Http\Controllers\Api\ApiTransformer;


class UserQuizResultsTransformer extends ApiTransformer
{
	protected $parent;

	public function __construct($parent = null)
	{
		$this->parent = $parent;
	}

	public function transform(UserQuizResults $results)
	{
		$data = [
			'question'   => $results->quizQuestion,
			'answer'     => $results->quizAnswer,
		];

		if ($this->parent) {
			$data = array_merge($data, $this->parent);
		}

		return $data;
	}
}
