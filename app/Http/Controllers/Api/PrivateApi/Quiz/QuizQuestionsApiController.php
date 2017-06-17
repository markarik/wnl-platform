<?php

namespace App\Http\Controllers\Api\PrivateApi\Quiz;

use Illuminate\Http\Request;
use App\Http\Controllers\Api\ApiController;

class QuizQuestionsApiController extends ApiController
{
	public function __construct(Request $request)
	{
		parent::__construct($request);
		$this->resourceName = config('papi.resources.quiz-questions');
	}
}
