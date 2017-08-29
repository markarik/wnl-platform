<?php

namespace App\Http\Controllers\Api\PrivateApi;

use App\Http\Controllers\Api\ApiController;
use Illuminate\Http\Request;
use App\Models\Taxonomy;

class QuizQuestionsApiController extends ApiController
{
	const AVAILABLE_FILTERS = [
		// 'quiz-planned',
		'quiz-resolution',
		'by_taxonomy-subjects',
		'by_taxonomy-exams',
		// 'by_taxonomy-tags',
	];

	public function __construct(Request $request)
	{
		parent::__construct($request);
		$this->resourceName = config('papi.resources.quiz-questions');
	}
}
